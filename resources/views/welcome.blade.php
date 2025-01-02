@php
    $dashboardUrl = \App\Filament\Pages\Dashboard::getUrl();
@endphp
    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Uppi</title>

    <meta name="description"
          content="Open-source uptime monitoring for websites and APIs. Monitor your website every minute and get notified when it goes down.">
    <meta name="keywords" content="uptime monitoring, website monitoring, api monitoring, open-source">
    <meta name="author" content="Janyk Steenbeek">

    <meta property="og:title" content="Uppi">
    <meta property="og:description"
          content="Open-source uptime monitoring for websites and APIs. Monitor your website every minute and get notified when it goes down.">
    <meta property="og:image" content="{{ asset('static/iPad.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@janyksteenbeek">
    <meta name="twitter:creator" content="@janyksteenbeek">
    <meta name="twitter:title" content="Uppi">
    <meta name="twitter:description"
          content="Open-source uptime monitoring for websites and services. Monitor your website every minute and get notified when it goes down.">
    <meta name="twitter:image" content="{{ asset('static/iPad.png') }}">

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}"/>

    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700&display=swap" rel="stylesheet"/>
    <script defer src="https://statisfyer.nl/script.js" data-website-id="5e2d6b2a-67a0-4965-ace2-8677b879fbdf"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body>
<div class="bg-white" x-data="{ open: false }">
    <header class="absolute inset-x-0 top-0 z-50">
        <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a class="-m-1.5 p-1.5">
                    <span class="sr-only">Uppi</span>
                    <img class="h-8 w-auto" src="{{ asset('logo.svg') }}"
                         alt="Uppi">
                </a>
            </div>
            <div class="flex lg:hidden">
                <button type="button"
                        x-on:click="open = !open"
                        class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                    <span class="sr-only">Open main menu</span>
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
            </div>
            <div class="hidden lg:flex lg:gap-x-12">
                <a href="#features"
                   class="text-sm/6 font-semibold text-gray-900">Features</a>
                <a href="https://github.com/janyksteenbeek/uppi/blob/main/README.md"
                   class="text-sm/6 font-semibold text-gray-900">Docs</a>
                <a href="https://github.com/sponsors/janyksteenbeek" class="text-sm/6 font-semibold text-gray-900">Sponsor</a>
                <a href="https://github.com/janyksteenbeek/uppi" class="text-sm/6 font-semibold text-gray-900">Contribute</a>
                <a href="{{ $dashboardUrl }}" class="text-sm/6 font-semibold text-gray-900">Sign
                    in</a>
                <a href="https://apps.apple.com/app/uppi/id6739699410"
                   class="text-sm/6 font-semibold text-gray-900 inline-flex items-center gap-0.5">
                    <svg class="w-4 h-4 " viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                </a>
                <a href="https://play.google.com/store/apps/details?id=dev.uppi.app"
                   class="text-sm/6 font-semibold text-gray-900 inline-flex items-center gap-0.5 -ml-8">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                    </svg>
                </a>
            </div>
            <div class="hidden lg:flex lg:flex-1 lg:justify-end">
                <a href="{{ $dashboardUrl }}" class="text-sm/6 font-semibold text-red-600">Get started <span
                        aria-hidden="true">&rarr;</span></a>
            </div>
        </nav>
        <!-- Mobile menu, show/hide based on menu open state. -->
        <div class="lg:hidden" role="dialog" aria-modal="true" x-show="open" x-cloak>
            <!-- Background backdrop, show/hide based on slide-over state. -->
            <div class="fixed inset-0 z-50"></div>
            <div
                class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <a href="#" class="-m-1.5 p-1.5">
                        <span class="sr-only">Uppi</span>
                        <img class="h-8 w-auto"
                             src="{{ asset('logo.svg') }}" alt="Uppi">
                    </a>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" x-on:click="open = false">
                        <span class="sr-only">Close menu</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                             aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            <a href="https://github.com/janyksteenbeek/uppi"
                               class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Source</a>
                            <a href="https://github.com/sponsors/janyksteenbeek"
                               class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Sponsor</a>
                        </div>
                        <div class="py-6">
                            <a href="{{ $dashboardUrl }}"
                               class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Log
                                in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="relative isolate pt-14">
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
             aria-hidden="true">
            <div
                class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-red-500 to-red-600 opacity-20 sm:left-[calc(50%-30rem)] sm:w-[79.1875rem]"
                style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
        <div class="py-24 sm:py-32 lg:pb-40">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <h1 class="text-balance text-5xl font-semibold tracking-tight text-gray-900 sm:text-7xl">
                        Be the <strong class="text-red-500">first</strong> to know when your website goes <strong
                            class="glitch" data-text="down">down</strong>
                    </h1>
                    <p class="mt-8 text-pretty text-lg font-medium text-gray-600 sm:text-xl/8">
                        Open-source uptime monitoring for websites and APIs. Monitor your website every minute and get
                        notified when it goes down.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="{{ url(\App\Filament\Pages\Dashboard::getUrl()) }}"
                           class="rounded-md bg-red-600 px-3.5 py-2.5 text-lg font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                            Start monitoring for free <span aria-hidden="true">→</span>
                        </a>
                        <a href="https://github.com/janyksteenbeek/uppi"
                           class="text-sm/6 font-semibold text-gray-900 inline-flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" class="size-5" viewBox="0 0 50 50">
                                <path
                                    d="M17.791,46.836C18.502,46.53,19,45.823,19,45v-5.4c0-0.197,0.016-0.402,0.041-0.61C19.027,38.994,19.014,38.997,19,39 c0,0-3,0-3.6,0c-1.5,0-2.8-0.6-3.4-1.8c-0.7-1.3-1-3.5-2.8-4.7C8.9,32.3,9.1,32,9.7,32c0.6,0.1,1.9,0.9,2.7,2c0.9,1.1,1.8,2,3.4,2 c2.487,0,3.82-0.125,4.622-0.555C21.356,34.056,22.649,33,24,33v-0.025c-5.668-0.182-9.289-2.066-10.975-4.975 c-3.665,0.042-6.856,0.405-8.677,0.707c-0.058-0.327-0.108-0.656-0.151-0.987c1.797-0.296,4.843-0.647,8.345-0.714 c-0.112-0.276-0.209-0.559-0.291-0.849c-3.511-0.178-6.541-0.039-8.187,0.097c-0.02-0.332-0.047-0.663-0.051-0.999 c1.649-0.135,4.597-0.27,8.018-0.111c-0.079-0.5-0.13-1.011-0.13-1.543c0-1.7,0.6-3.5,1.7-5c-0.5-1.7-1.2-5.3,0.2-6.6 c2.7,0,4.6,1.3,5.5,2.1C21,13.4,22.9,13,25,13s4,0.4,5.6,1.1c0.9-0.8,2.8-2.1,5.5-2.1c1.5,1.4,0.7,5,0.2,6.6c1.1,1.5,1.7,3.2,1.6,5 c0,0.484-0.045,0.951-0.11,1.409c3.499-0.172,6.527-0.034,8.204,0.102c-0.002,0.337-0.033,0.666-0.051,0.999 c-1.671-0.138-4.775-0.28-8.359-0.089c-0.089,0.336-0.197,0.663-0.325,0.98c3.546,0.046,6.665,0.389,8.548,0.689 c-0.043,0.332-0.093,0.661-0.151,0.987c-1.912-0.306-5.171-0.664-8.879-0.682C35.112,30.873,31.557,32.75,26,32.969V33 c2.6,0,5,3.9,5,6.6V45c0,0.823,0.498,1.53,1.209,1.836C41.37,43.804,48,35.164,48,25C48,12.318,37.683,2,25,2S2,12.318,2,25 C2,35.164,8.63,43.804,17.791,46.836z"></path>
                            </svg>

                            janyksteenbeek/uppi
                        </a>
                    </div>
                </div>
                <div class="mt-16 flow-root sm:mt-24">
                    <div
                        class="-m-2 rounded-xl bg-gray-900/5 p-2 ring-1 ring-inset ring-gray-900/10 lg:-m-4 lg:rounded-2xl lg:p-4">
                        <img src="{{ asset('static/screenshot-dashboard.png') }}"
                             alt="App screenshot" width="2432" height="1442"
                             class="rounded-md shadow-2xl ring-1 ring-gray-900/10">
                    </div>
                </div>
            </div>
        </div>
        <div
            class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
            aria-hidden="true">
            <div
                class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-red-300 to-red-700 opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"
                style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </div>
</div>

<div class="overflow-hidden bg-white py-24 sm:py-32" id="features">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div
            class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2">
            <div class="lg:ml-auto lg:pl-4 lg:pt-4">
                <div class="lg:max-w-lg">
                    <p class="mt-2 text-pretty text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">Uppi has
                        everything you need to monitor your services.</p>
                    <p class="mt-6 text-lg/8 text-gray-600">Features you expect from a world-class uptime monitoring
                        service, and all for free.</p>
                    <dl class="mt-10 max-w-xl space-y-5 text-base/7 text-gray-600 lg:max-w-none">
                        <div class="relative ">
                            <dt class="inline font-semibold text-gray-900">
                                HTTP & TCP monitoring.
                            </dt>
                            <dd class="inline">
                                Different monitoring types for every use case. Monitor your website, API, or any other
                                service.
                            </dd>
                        </div>

                        <div class="relative ">
                            <dt class="inline font-semibold text-gray-900">
                                Alert Routing.
                            </dt>
                            <dd class="inline">
                                Set up alert routing rules for each individual monitor. Get notified via the Uppi mobile
                                app, email, SMS, Slack, Pushover or Bird.
                            </dd>
                        </div>

                        <div class="relative ">
                            <dt class="inline font-semibold text-gray-900">
                                Uptime & Response Time.
                            </dt>
                            <dd class="inline">
                                Track your website's uptime and response time. Get insights into your website's
                                performance.
                            </dd>
                        </div>

                        <div class="relative ">
                            <dt class="inline font-semibold text-gray-900">
                                Mobile App.
                            </dt>
                            <dd class="inline">
                                Get notified when your website goes down, wherever you are. Available for iOS and
                                Android.
                            </dd>
                        </div>

                        <div class="relative ">
                            <dt class="inline font-semibold text-gray-900">
                                Public Status Pages.
                            </dt>
                            <dd class="inline">
                                Share your website's status with your users. Customize your status page to match your
                                brand. Embed it on your website, or use your public status page on Uppi <a
                                    href="https://uppi.dev/s/uppi" class="underline text-red-500">like this one</a>.
                            </dd>
                        </div>

                        <div class="relative ">
                            <dt class="inline font-semibold text-gray-900">
                                Minute-by-minute monitoring & thresholds.
                            </dt>
                            <dd class="inline">
                                Monitor your website every minute. Set up your own interval & thresholds to prevent
                                false-positive alerts.
                            </dd>
                        </div>

                        <div class="relative ">
                            <dt class="inline font-semibold text-gray-900">
                                Open-source.
                            </dt>
                            <dd class="inline">
                                Uppi is licensed under CC-BY-NC. You can host Uppi yourself, or use the hosted
                                version for free.
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            <div class="flex items-center justify-end lg:order-first">
                <img src="{{ asset('static/screenshot-edit.png') }}"
                     alt="Product screenshot"
                     class="w-[48rem] max-w-none rounded-xl shadow-xl ring-1 ring-gray-400/10 sm:w-[57rem]" width="2432"
                     height="1442">
            </div>
        </div>
    </div>
</div>


<footer class="bg-white">
    <div class="mx-auto max-w-7xl overflow-hidden py-20 px-6 sm:py-24 lg:px-8">
        <nav class="-mb-6 columns-2 sm:flex sm:justify-center sm:space-x-12" aria-label="Footer">
            <div class="pb-6"><a href="https://www.webmethod.nl/juridisch/algemene-voorwaarden"
                                 class="text-sm leading-6 text-gray-600 hover:text-gray-900">Terms</a>
            </div>
            <div class="pb-6"><a href="/privacy"
                                 class="text-sm leading-6 text-gray-600 hover:text-gray-900">Privacy</a></div>
            <div class="pb-6"><a href="https://www.webmethod.nl/juridisch/coordinated-vulnerability-disclosure"
                                 class="text-sm leading-6 text-gray-600 hover:text-gray-900">Coordinated Vulnerability
                    Disclosure</a></div>
            <div class="pb-6"><a href="https://github.com/sponsors/janyksteenbeek"
                                 class="text-sm leading-6 text-gray-600 hover:text-gray-900">Sponsor</a></div>
            <div class=" pb-6"><a href="https://github.com/janyksteenbeek/uppi"
                                  class="text-sm leading-6 text-gray-600 hover:text-gray-900">GitHub</a></div>
            <div class=" pb-6"><a href="https://x.com/janyksteenbeek">𝕏</a></div>
        </nav>
        <div class="mt-10 flex justify-center"><a href="https://www.webmethod.nl?utm_source=uppi&utm_medium=footer"
                                                  class="text-gray-400 hover:text-gray-500"><img
                    src="https://www.webmethod.nl/assets/images/logo/logo.png"
                    alt="Webmethod"
                    class="h-5"></a></div>
        <p class="mt-10 text-center text-xs leading-5 text-gray-500">© {{ date('Y') }} Webmethod ·
            KVK 63314061 · BTW-ID NL002401656B67</p></div>
</footer>
</body>
</html>
