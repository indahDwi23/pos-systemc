<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>
        {{
            Request::is('transaction/create') ? 'Make Order' :
            (Request::is('transaction/*') ? 'Payment' : 'appantuh')
        }}
    </title>
    
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="/images/logofood.ico">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#181818">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kasir Sultan">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/logofood.png"> 
    
    <!-- FontAwesome JS-->
    <script defer src="/plugins/fontawesome/js/all.min.js"></script>

    <!-- App CSS -->  
    <link rel="stylesheet" href="/css/portal.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/fontawesome-free-6.2.1-web/css/all.css">
    <link rel="stylesheet" href="/css/payment.css">
</head> 

<body>
    <header>   
        @include('layouts.sidebar')
    </header>
    <div class="app-wrapper">
        <div class="app-content">
            <div class="container-xl page-order">
                <div class="row h-100">
                    @yield('container')
                </div>
            </div>
        </div>
    </div>
    <script src="/plugins/popper.min.js"></script>
    <script src="/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/app.js"></script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
</body>