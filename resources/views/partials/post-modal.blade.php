<style>
  /* Modal-specific styles copied from dashboard so modal looks identical across pages */

  .post-modal-glass {
    background: #fff;
    border-radius: 0.7rem;
    box-shadow: 0 8px 32px 0 rgba(216, 27, 96, 0.13), 0 2px 16px 0 rgba(66, 46, 89, 0.08);
    border: 1.5px solid #ffd1e3;
    overflow: hidden;
  }

  .animate-modal {
    animation: modalPop .32s cubic-bezier(.4, 1.6, .6, 1) 1;
  }

  @keyframes modalPop {
    0% {
      transform: scale(.96) translateY(32px);
      opacity: 0;
    }

    100% {
      transform: scale(1) translateY(0);
      opacity: 1;
    }
  }

  .post-modal-header {
    background: #ffe3ef;
    border-bottom: 1.5px solid #ffd1e3;
    padding-top: 1rem;
    padding-bottom: 1rem;
    border-radius: 0.7rem 0.7rem 0 0;
  }

  #postModal .modal-title {
    color: #d81b60;
    font-size: 1.7rem;
    letter-spacing: -0.5px;
  }

  #postModalType {
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    background: #d81b60 !important;
    border-radius: 0.5rem;
    padding: 0.35em 1em;
    font-weight: 600;
    box-shadow: 0 2px 8px 0 rgba(216, 27, 96, 0.08);
  }

  .post-modal-imgwrap {
    background: #ffe3ef;
    border-top-left-radius: 0.7rem;
    border-top-right-radius: 0.7rem;
    overflow: hidden;
  }

  #postModalImageWrap img,
  #postModalImageWrap iframe {
    width: 100%;
    height: 260px;
    min-height: 140px;
    max-height: 260px;
    object-fit: cover;
    border-top-left-radius: 0.7rem;
    border-top-right-radius: 0.7rem;
    box-shadow: 0 2px 16px 0 rgba(216, 27, 96, 0.08);
    background: #fff;
    display: block;
  }

  #postModalImageWrap iframe {
    aspect-ratio: 16/9;
  }

  @media (max-width: 991px) {

    #postModalImageWrap img,
    #postModalImageWrap iframe {
      height: 140px;
      max-height: 140px;
    }
  }

  .post-modal-body {
    font-size: 1.08rem;
    color: #a0003a;
    line-height: 1.65;
  }

  #postModalDesc {
    font-size: 1.08rem;
    color: #a0003a;
    line-height: 1.65;
    word-break: break-word;
  }

  #postModalDesc p,
  #postModalDesc h1,
  #postModalDesc h2,
  #postModalDesc h3,
  #postModalDesc ul,
  #postModalDesc ol,
  #postModalDesc blockquote,
  #postModalDesc pre {
    margin-bottom: 0.8rem;
  }

  #postModalDesc ul,
  #postModalDesc ol {
    padding-left: 1.25rem;
  }

  #postModalDesc a {
    color: #b8004c;
    text-decoration: underline;
  }

  #postModalLinks a {
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    border-radius: 1.2em;
    font-weight: 600;
    padding: 0.45em 1.2em;
    font-size: 1rem;
    box-shadow: 0 2px 8px 0 rgba(216, 27, 96, 0.08);
    transition: background .18s, color .18s, box-shadow .18s;
  }

  #postModalLinks a.btn-primary {
    background: #d81b60;
    border: none;
    color: #fff;
  }

  #postModalLinks a.btn-primary:hover {
    background: #b8004c;
    color: #fff;
  }

  #postModalLinks a.btn-danger {
    background: #ff5252;
    border: none;
    color: #fff;
  }

  #postModalLinks a.btn-danger:hover {
    background: #b8004c;
    color: #fff;
  }

  @media (max-width: 767px) {
    #postModal .modal-title {
      font-size: 1.08rem;
    }


    .post-modal-glass {
      border-radius: 0.3rem;
    }

    .post-modal-header {
      border-radius: 0.3rem 0.3rem 0 0;
    }
  }


  /* Make modal larger for better image viewing */
  #postModal .modal-dialog {
    max-width: 1000px;
    /* wider modal */
    width: 95%;
    /* responsive */
  }

  @media (max-width: 991px) {
    #postModal .modal-dialog {
      max-width: 95%;
    }
  }

  /* Optional: make image taller inside modal for clarity */
        #postModalImageWrap img,
        #postModalImageWrap iframe {
            width: 100%;
            height: auto;
            max-height: 550px;
            /* Bigger modal image */
            object-fit:scale-down;
            /* Keeps full image visible */
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            box-shadow: 0 2px 16px 0 rgba(216, 27, 96, 0.08);
            background: #fff;
            display: block;
            margin: 0 auto;
        }

  #postModal .modal-content {
    padding: 1.5rem 2rem;
  }

  #postModal .modal-dialog {
    max-width: 900px;
    /* wider modal */
  }

  #postModal .modal-body {
    max-height: 80vh;
    overflow-y: auto;
  }
</style>

<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content post-modal-glass animate-modal">
      <div class="modal-header border-0 post-modal-header">
        <div class="d-flex align-items-center w-100">
          <div class="flex-grow-1">
            <h5 class="modal-title fw-bold mb-0" id="postModalLabel"></h5>
            <span id="postModalType" class="badge text-white ms-1 mt-2"></span>
          </div>
          <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body p-0">
        <div id="postModalImageWrap" class="w-100 post-modal-imgwrap"></div>
        <div class="p-4 post-modal-body">
          <div id="postModalDesc" class="mb-3"></div>
          <div id="postModalLinks" class="d-flex flex-wrap gap-2"></div>
        </div>
      </div>
    </div>
  </div>
</div>