<!--extends('layouts.templates.contained')-->

<!-- section('content')-->
<style>
    body{
        display:none;
    }
</style>
<div class="payment" style="display:none;">
        <h2>Pradedamas apmokėjimo procesas</h2>
        <p>Peradresuojama į apmokėjimo tinklalapį...</p>
        <form action="%1$s" method="post" style="visibility: hidden">
            <p>Redirecting to payment page...</p>
            <p>
                %2$s
                <input type="submit" value="Continue" />
            </p>
        </form>
    </div>
    <script>
        window.onload = function() {
            document.forms[0].submit()
        }
    </script>

<!-- endsection-->
