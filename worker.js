// 借鉴自 https://github.com/heymind/OneDrive-Index-Cloudflare-Worker/blob/96ec39a46c277d4e6fe8cec89bbd2363f65f1e4a/index.js

const cache = caches.default;
const entireFileCacheLimit = 10000000; // 10MB
const chunkedCacheLimit = 100000000; // 100MB
const defaultSharePoint = 'ixnah-my.sharepoint.com'; // 请改成自己的OneDrive域名

addEventListener('fetch', event => {
  event.respondWith(handleRequest(event.request))
})

/**
 * Respond to the request
 * @param {Request} request
 */
async function handleRequest(request) {
  let requestUrl = new URL(request.url);
  let cacheRequest = null;
  if (requestUrl.searchParams.has('odPath')) {
    let cacheUrl = new URL(requestUrl.protocol + "//" + requestUrl.hostname + "/" + requestUrl.searchParams.get('odPath'));
    cacheRequest = new Request(cacheUrl, request);
    requestUrl.searchParams.delete('odPath');
  }
  let maybeResponse = null;
  if(cacheRequest) {
    maybeResponse = await cache.match(cacheRequest);
  } else {
    maybeResponse = await cache.match(request);
  }
  if (maybeResponse) {
    return maybeResponse;
  }

  let fileSize = 104857600; // 不传参数就默认不走cache
  if (requestUrl.searchParams.has('odFileSize')) {
    fileSize = requestUrl.searchParams.get('odFileSize');
    requestUrl.searchParams.delete('odFileSize');
  }
  let sharePointHost = requestUrl.searchParams.get('odHost');
  requestUrl.searchParams.delete('odHost');
  if (sharePointHost == null || sharePointHost.indexOf('.') == -1) {
    sharePointHost = defaultSharePoint;
  }
  requestUrl.hostname = sharePointHost;

  if(cacheRequest) {
    return setCache(cacheRequest, fileSize, requestUrl.toString(), proxiedDownload);
  } else {
    return setCache(request, fileSize, requestUrl.toString(), proxiedDownload);
  } 
}

/**
 * Cache downloadUrl according to caching rules.
 * @param {Request} request client's request 
 * @param {integer} fileSize 
 * @param {string} downloadUrl 
 * @param {function} fallback handle function if the rules is not satisfied
 */
async function setCache(request, fileSize, downloadUrl, fallback) {
  if (fileSize < entireFileCacheLimit) {
    console.info(`Cache entire file ${request.url}`);
    const remoteResp = await fetch(downloadUrl);
    const resp = new Response(remoteResp.body, {
      headers: {
        "Content-Disposition" : remoteResp.headers.get("Content-Disposition"),
        "Content-Length": remoteResp.headers.get("Content-Length"),
        "Content-Type": remoteResp.headers.get("Content-Type"),
        "ETag": remoteResp.headers.get("ETag")
      },
      status: remoteResp.status,
      statusText: remoteResp.statusText,
    });
    await cache.put(request, resp.clone());
    return resp;

  } else if (fileSize < chunkedCacheLimit) {
    console.info(`Chunk cache file ${request.url}`);
    const remoteResp = await fetch(downloadUrl);
    let {
      readable,
      writable
    } = new TransformStream();
    remoteResp.body.pipeTo(writable);
    const resp = new Response(readable, {
      headers: {
        "Content-Disposition" : remoteResp.headers.get("Content-Disposition"),
        "Content-Length": remoteResp.headers.get("Content-Length"),
        "Content-Type": remoteResp.headers.get("Content-Type"),
        "ETag": remoteResp.headers.get("ETag")
      },
      status: remoteResp.status,
      statusText: remoteResp.statusText
    });
    await cache.put(request, resp.clone());
    return resp;

  } else {
    console.info(`No cache ${request.url} because file_size(${fileSize}) > limit(${chunkedCacheLimit})`);
    return await fallback(downloadUrl);
  }
}

/**
 * Download a file using Cloudflare as a relay.
 * @param {string} downloadUrl 
 */
async function proxiedDownload(downloadUrl) {
  console.info(`ProxyDownload -> ${downloadUrl}`);
  const remoteResp = await fetch(downloadUrl);
  let {
    readable,
    writable
  } = new TransformStream();
  remoteResp.body.pipeTo(writable);
  return new Response(readable, remoteResp);
}
