<footer {{ $attributes->merge(['class' => '']) }}>
    <div class="">
        <div class="copy">© {{ date('Y') }} <a class="text-capitalize" href="{{ config('app.url') }}"
                target="_blank">{{'Green Drive EV' }}</a>.</div>
        <div class="credit">{{ localize('Designed & Developed by') }}: <a href="https://alabtechnology.com/"
                target="_blank">{{ localize('Alabtechnology') }}<a></div>
    </div>
</footer>
