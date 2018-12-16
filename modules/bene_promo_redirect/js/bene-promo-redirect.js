let validReferrer = false;
if (document.referrer === "") {
  validReferrer = true;
}
else {
  let ref = new URL(document.referrer);
  if (ref.hostname !== drupalSettings.benePromoRedirect.destinationDomain && ref.hostname !== window.location.hostname) {
    validReferrer = true;
  }
}

if (validReferrer) {
  let urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('promo_redirect') !== 'no') {
    window.location.href = drupalSettings.benePromoRedirect.destination;
  }
}
