<div class="top-companies section">
    <div class="container">
        <recommended
            :companies="{{ $companies }}"
            :inquiries="{{ collect($inquiries)->toJson() }}"
            broker-get-route="{{ route('companies.index') }}"
            :islogin="{{ Auth::check()?1:0 }}"
            :isbroker="{{(Auth::check() && Auth::user()->isBroker())?1:0}}"
        />
    </div>
</div>
