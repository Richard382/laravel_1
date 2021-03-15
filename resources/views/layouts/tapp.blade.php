<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="verify-paysera" content="ba16b2098bec52513275f0e72e599735">
    <meta name="description" content="{{ setting('site.description') }}">

    <!-- Fav Icon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.ico') }}" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.ico') }}" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @hasSection('pageTitle')
        <title>@yield('pageTitle') - {{ setting('site.title') }}</title>
    @else
        <title>{{ setting('site.title') }}</title>
    @endif

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap&subset=latin-ext" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vuetify/2.2.19/vuetify.min.css" integrity="sha256-GBRlnAGxmcL6qTG+hrAHLonE7iOWagXDzORo+/NFK5s=" crossorigin="anonymous" />
{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vuetify/2.1.12/vuetify.min.css" integrity="sha256-QAvBxOUdSj+MaIojNHfbf9a3HhORmrLlgFqgMiKMEb0=" crossorigin="anonymous" />--}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>

    @if(Auth::check())
        <script>
            window.OneSignal = window.OneSignal || [];
            OneSignal.push(function() {
                OneSignal.init({
                    appId: "03663e08-3c28-42f9-a990-7e5326a43eb8",
                });
            });
        </script>
    @endif
    <style>
    .form--getanoffer .v-input__append-inner {
        position: absolute;
        width: 100%;
    }
    .v-divider {
        padding-bottom: 20px;
    }
    .form--getanoffer .v-input__append-inner .v-input__icon--append {
        justify-content: flex-end;
    }
    .notification-wrapper {
        max-height: 300px;
        overflow: auto;
    }
    @media only screen and (max-width: 600px) {
/*        .form--getanoffer [aria-expanded="true"] .v-input__append-inner{
            position: fixed;
            top: 140px;
            right: 43%;
            z-index: 9;
            background: #fff;
            width: 70px;
            height: 70px;
            box-shadow: -1px 1px 8px 5px rgba(0,0,0,0.5);
            border-radius: 100%;
        }
        .form--getanoffer [aria-expanded="true"] .v-input__append-inner .v-input__icon.v-input__icon--append{
            margin-right: 22px;
            margin-top: 12px;
        }
        .form--getanoffer [aria-expanded="true"] .v-input__append-inner .v-input__icon.v-input__icon--append i.v-icon.primary--text
        
        {
            color: #08b973 !important;
        }
        .form--getanoffer [aria-expanded="true"] .v-input__append-inner:after {
            content: "UŽDARYTI";
            position: fixed;
            right: 44.2%;
            top: 180px;
            font-size: 12px;
        }*/
    }
    .v-badge--bell{
        width: 20px;
    }
    .inquiry-id {
        color: #333 !important;
    }
    .menu-with-title .v-responsive.v-image{
        margin-bottom: 15px;
        overflow: visible;
    }
    .menu-with-title .subheading{
        /* width: 22px;
        top: 24px;
        */
        left: -50px; 
        position: absolute;
        color: #fff;
    }
    .icon--person.menu-with-title .v-responsive.v-image{
        margin-right: 25px;
    }
    .icon--person.menu-with-title .subheading{
        width: 92px;
        /* top: 24px; */
        /* left: -35px; */
        right: 0;
        position: absolute;
        color: #fff;
        text-align: center;
    }
    .item-border--red {
        border-left: 3px solid red;
    }
    .item-border--blue {
        border-left: 3px solid mediumblue;
    }
    .item-border--gray {
        border-left: 3px solid gray;
    }
    .create-inq-prov-link {
        font-weight: bold;
        margin-top: -25px;
        width: 50%;
        float: right;
        text-align: center;
    }
    .create-inq-cust-link {
        font-weight: bold;
        margin-top: -25px;
        /*width: 50%;*/
        /*float: right;*/
        text-align: center;
    }
    .clearfix {
        clear: both;
    }
    .inquiry-expand {
        display: inline-block;
        float: right;
        position: absolute;
        right: 90px;
        top: 8px;
    }
    .inquiry-expand button{
        margin: 0 !important;
        height: 22px !important;
        margin-left: 5px !important;
    }
    .inquiries__list-item:hover {
        cursor: pointer;
    }
    .notification_close{
        font-size: 25px;
        display: inline-block;
        position: absolute;
        top: 0;
        right: 20px;
        padding: 0 15px;
        cursor: pointer;
    }
    .notification-list__item{
        position:relative;
    }
    .notification-list__item .v-list-item__content:after{
        content: "";
        display: inline-block;
        border-top: 2px solid;
        border-right: 2px solid;
        height: 10px;
        width: 10px;
        transform: rotate(45deg);
        position: absolute;
        top: 13px;
        right: 70px;
        border-color: #08b973;
    }
    .inquiries__list.loader-manage{
        position:relative;
    }
    .inquiries__list .inquiry-search-loader{
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
        background: rgba(255,255,255,0.5);
        height: 100%;
        width: 100%;
        text-align: center;
        padding-top: 100px;
    }
    .inquiries__list .inquiry-search-loader i{
        position: sticky;
        top: 120px;
    }
</style>
    <style>
        @media screen and (max-width: 420px) {
            .switch-type-tab .v-tab{
                max-width:170px;
            }
        }
        .v-tabs-slider-wrapper {
            max-width: 0px !important;
        }
        .v-slide-group__prev.v-slide-group__prev--disabled {
            display: none !important;
        }
        header {
            margin: 40px !important;

        }
        header .v-toolbar__content {
            height: 300px;
            /* background: red; */
            position: relative;
        }

        #homepageImg {
            width: 100%;
        }
        .kaina-num {
            font-size: 2rem;
            color: #A00E0E;
        }
        .card-letter-box {
            background: #EFF0FF !important;
        }
        .center-box {
            background: #363A7B !important;
        }
        .center-box .v-input__slot {
            background: white !important;
        }
        .center-box .headline {
            color: white !important;
        }
        .size-2rem {
            /* font-size: 2rem; */
        }
        .red-text {
            color: #A00E0E;
        }
        /* common */
        
/* common */
.ribbon {
  width: 250px;
  height: 250px;
  overflow: hidden;
  position: absolute;
}
.ribbon::before,
.ribbon::after {
  position: absolute;
  z-index: -1;
  content: '';
  display: block;
  border: 5px solid #2980b9;
}
.ribbon span {
  position: absolute;
  display: block;
  width: 300px;
  padding: 15px 0;
  background-color: #FF5C5C;
  box-shadow: 0 5px 10px rgba(0,0,0,.1);
  color: #fff;
  font: 700 18px/1 'Lato', sans-serif;
  text-shadow: 0 1px 1px rgba(0,0,0,.2);
  /* text-transform: uppercase; */
  text-align: center;
}

/* top left*/
.ribbon-top-left {
  top: -35px;
  left: -30px;
}
.ribbon-top-left::before,
.ribbon-top-left::after {
  border-top-color: transparent;
  border-left-color: transparent;
}
.ribbon-top-left::before {
  top: 0;
  right: 0;
}
.ribbon-top-left::after {
  bottom: 0;
  left: 0;
}
.ribbon-top-left span {
  right: 20px;
  top: 63px;
  transform: rotate(-45deg);
}


    #app 
    {
        background-image: url(/images/homepage/header.jpg);
        width: 100%;
        background-size: contain;
        background-repeat: no-repeat;
        background-color: #A5ADBB;
    }
    .main_content {
        padding: 150px 0px 0px;
    }
    @media only screen and (max-width: 600px) {
        .main_content {
            padding: unset;
        }
    }
    .select_index_circle {
        top: 7px;
    }
    .specialist_btn {
        margin-left: -30px;
        width: 150px;
    }
    .youtube_area {
        background: white;
    }
    .youtube_link_title_bold_green {
        color: #08B973;
    }
    .service_title {
        color: #363A7B;
        font-size: 16px;
    }
    </style>
</head>
<body>
    <!-- <img id="homepageImg" src="./images/homepage/header.jpg" /> -->
    <div id="app" v-cloak>

        <v-app>
            @include('layouts.partials.header')

            @yield('template')
            @yield('template-footer')
            @include('layouts.partials.tfooter')

            @include('partials.back-to-top')
        </v-app>

    </div>

    <!-- Scripts -->
    @if(Auth::check())
        <script type="application/javascript">
            window.auth = {
              role: '{{ Auth::user()->role ? Auth::user()->role->slug : "None"}}',
              price: '{{ config('site.price.amount') }}',
              currency: '{{ config('site.price.currency') }}'
            };
        </script>
    @endif
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BMWWFGQGZ2"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-BMWWFGQGZ2');
    </script>
</body>
</html>
