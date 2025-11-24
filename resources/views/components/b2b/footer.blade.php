
<footer>
  <div class="footer-text">
    <div class="row text-center text-md-start align-items-center">
      <div class="col-12 col-md-6 mb-2 mb-md-0">
        <div class="copy">
          © {{ date('Y') }}
          <a class="text-capitalize text-black" href="{{ config('app.url') }}" target="_blank">
            {{ localize('Green Drive EV') }}
          </a>.
        </div>
      </div>

      <div class="col-12 col-md-6 text-center text-md-end">
        <div class="credit">
          @localize('Designed_and_Developed_by'):
          <a class="text-black text-capitalize" href="https://alabtechnology.com/" target="_blank">
            {{ localize('Alabtechnology') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</footer>
