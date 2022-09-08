<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="cookieNotification" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false" data-bs-delay="600000">
    <div class="toast-header">
      <strong class="me-auto"><?= __('Cookie legacy agreement') ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      <p><?= __("This website is using cookie to perform authorization and other special features. Continuing to use website you are agree with cookie usage notification.") ?></p>
      <button type="button" class="btn btn-primary" id="cookieOk"><?= __('Agree') ?></button>
      <button type="button" class="btn btn-danger" data-bs-dismiss="toast" aria-label="Close"><?= __('Dismiss') ?></button>
    </div>
  </div>
</div>