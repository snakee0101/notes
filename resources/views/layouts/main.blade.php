<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="h-screen antialiased leading-none font-sans flex flex-col">
<div id="app">
    <header class="flex flex-row justify-between py-2 px-3 shadow-sm items-center">

        <!--TODO: Make a top menu. There should be:-->
        <section class="flex flex-row items-center">
            <a href="" class="pt-2 px-2 pb-3 rounded-full hover:bg-gray-200">
                <svg class="icon icon-menu" viewBox="0 0 32 32">
                    <path d="M2 6h28v6h-28zM2 14h28v6h-28zM2 22h28v6h-28z"></path>
                </svg>
            </a>
            <img src="{{ asset('keep.png') }}" alt="">
            <span class="text-xl">Keep</span>
        </section>

        <search-component></search-component>

        <section class="flex flex-row items-center">
            <a href="" class="p-2 mr-3 rounded-full hover:bg-gray-200">
                <svg class="icon icon-cog" viewBox="0 0 32 32">
                    <path d="M29.181 19.070c-1.679-2.908-0.669-6.634 2.255-8.328l-3.145-5.447c-0.898 0.527-1.943 0.829-3.058 0.829-3.361 0-6.085-2.742-6.085-6.125h-6.289c0.008 1.044-0.252 2.103-0.811 3.070-1.679 2.908-5.411 3.897-8.339 2.211l-3.144 5.447c0.905 0.515 1.689 1.268 2.246 2.234 1.676 2.903 0.672 6.623-2.241 8.319l3.145 5.447c0.895-0.522 1.935-0.82 3.044-0.82 3.35 0 6.067 2.725 6.084 6.092h6.289c-0.003-1.034 0.259-2.080 0.811-3.038 1.676-2.903 5.399-3.894 8.325-2.219l3.145-5.447c-0.899-0.515-1.678-1.266-2.232-2.226zM16 22.479c-3.578 0-6.479-2.901-6.479-6.479s2.901-6.479 6.479-6.479c3.578 0 6.479 2.901 6.479 6.479s-2.901 6.479-6.479 6.479z"></path>
                </svg>
            </a>

            <a href="" class="p-2 rounded-full hover:bg-gray-200">
                <img class="rounded-full"
                     style="width: 32px; height: 32px"
                     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAY1BMVEUejL7///8AhrsAhLoAgbmVwtwAh7sAhbsXir31+vzm8ffQ5O+y0uWrzuLZ6fLf7fTu9vrF3etFm8aEuNY1lcO61+dRoMl4s9NrrM9iqM3z+PsmkMB1sdKgyN86l8SOvtlZo8qTGV0CAAANWklEQVR4nN2d6ZaqOBSFQ4IxDig4l2jp+z9lAyqCJJB9kmDd3v2j1+pepfnMfKaw6P8uNuJ3zbPkdjxub5PJ5LrPVuvzKN86GuFim8ecy0rFvzkXIua/p22SzcN+8UiEy52QTCNVoHJ2uC3Xwb56FMJ9znV4NWbBKeT9ugry5SMQzk6ij+/dnUL9JP6HbHjCeX8HtjuTx783z10ZnHAllS3gQ1LkN5+zMjTh2roDG+Jik8x8tSAwYapdQYdVDNfLwk8TAhNuwCHahBS7xEcTwhJOpmTAUnx6cz/3BCWcuwEWktyZMSjhhTgLW/3Ib26LTkjCNHYHLBnlxKUVIQlvlJ1CI8WZw5oTkjD3A1gx7sh7R0DCldVx1FbiQpyOAQmvHtaZhqRYkpoRkPBE3+31EoeU0IyAhDvPgOUx54o3IyCh12n4FMe7MSChn93wQ0ruwWb8a4TFSN1izfjnCIuRuoFG6j9IWIxUZPv/FwmLkQqc4jDC9f46uU2udkbccITF1ngMQDhP7kxMJS//EfnPcvAQFZKw2DZ8Ey7uMVfvQ4pSXBwHLGJBCZncWa43doTrg+gewaQ49vZjWMLiR7azHlsRTmL9EVPK7HuExZJqZTu2Ibwbb7Iqvn2PsFhvbBCHCWe/fbcgcfoioeIWG+Mg4WzXfwniRsTwhMXGOIw4RDj7HbrlcdNAHYHQpheHCDfDF/XYcNofg7DoxSEvzgDh3cISoaTeaDsKYfH1A/tiP+HJyh4oL18lzPtPV72EN8trun6kjETI1IZMmNjaIZR2PR2LkEnzjtVPuLBvYqybiaMRmpfzfsIUcE9z3X1tPELjct5PmAPmTqW7y4xIyLh5zzAS2i2jTympWc/GJFQ5THjFrJ26M/CYhEz+gIRr0JzLNRNhVEJmNN0YCJFJWIprnJjjEho2ZRPhEXUbSc16jRAqdy+O2gGEGexykBpDNECo7ht97CIirjeG6whn+C/q2If8Gq2OnDt2pP6yqCMkhFA4zsPqz2dX5uj4124ZGkJ8jBZN1PhnUcJCy9ypH3VzRUdICTDQDRACYcGoXOajbj3tEt4oX8E1R28SYRRNHLpRt552COcU1632ikYkjOY687PtB3X3/Q7hnfLpUudfpxIWQ1VSh6pSnQPyJyFlmSluLzoDO53QNjRc90mdxeaTcMA6qpe6awBdCKMooW4cnR+b+fhg/VbrRBitGW0yqs9Lxgch6WO1919XwmhmYarV6fMi1ya80rpQ7+ZyJAQv4bU+f+82IakLTYFKzoTRjXQB+5gzLUJSF3LT7dqd0N6e2dRHJ7YIKV2ofg2APgijhNKL7U5sEiaEwPMem7oPwmhJQGwvp01CwpFb5Wa/iBdC1CRWqXUAbxBSjjN9jh8/hNEEb5ZsRts0CA/wLFQ9hlhvhNEFX/9EY+q8CVEDYqG4LxTDGyHhp2/eBN6EW/gIMRBd5o1whjasZc94E+L2Nb1j1D8hIepfvEdXTbhER/uAY9InIb6gNjaMmhAe7Hwo6MojIX4vj+u15kUIGy+Gsx98EsIm3Lc540WIpn90rmFhCaM92APvw+mLEE2O0BnXQhJGP2An1le6JyE6SLVu7aCEKdjCekt8EoKDdHAd9U8YTbDFvm7ikxDMSI5tguU9E6IXA5E2CcEhYLHMBCBcYo18uVIehOB2bzDMBCYEV8NXNzwIsUQ6NXBcC0WIXe+UahJi+6nWwj0CYTQY69rS05hREWInW7tZGIJwD02m54dWhJil2yp+PAghNtaex5qKEJqGVnthIEJsT+SzmhCyIk5tE44DEM6xwbZ4Ec6hyBdpCRiCELtFPQ5uJSE0gaV11lgIwj1i0n0E9paEkOfeIsMhIGEEnZ/zFyFyvX/to98ihBbFKnS5JEQWGm3IyoiE0DCt0k0KwhSZhrzXRBqecAY1NnkQLqA/sq+/EYQQmlLVgGPYiUYfkzAmIbLpV6cahhm7gaYEIlwAZ+gq+pthNh6bhL+whNBELDOyGHaxBKZhIELI4FI6EgtC5EcxRBqPSQhNqqwknCHRyna3+6CEyMJY2moY5DfURuiNTIgsNeXnMugvpvb7PXSERH455IBSbogMOgfZXu8rASuYLiHFKGRDPJWEyLgWSEUx4JBsf2HBfrnygMIQg35P/pRGwOnDws/zFnALLi0uLJoA8wXZLJAJboyr0ukIbIh5SQjcf62NUJXsvZq6fBSzkAazkhDIcbK1lD5lPRGhBQw6e/OS8AIM696U4o6shyk0+CFPYByW0DZoXJdw0yPEjRTPylEKECKHtsja+Q6t0JghQ6QFIXCSRQntLuQCrKKXIYRrkBAcpVbhAeYMXh+Ec5AQbYxNMFM3y2VAiAG7IkS2F5gwOg784GoKPxSArDTVPAS2F0NiRa9++p+2sIp5aAvaLc7YuRQ7XT3Vl8KkpoQCyIgTotoPk1An75cmxqswzynPPCBmDFESIsMaMUS9tdhpkyZVbF/8sCnANlh6WRi6+JI04Z3fXYkNsUQ3YhvclYRImAJiLm1pNsmFfN81FI9/EINIS6DRG7NEgSfIlla3g5oKwYWQu8uSUrf6IcRO87BiINZEwLemb916ka3mbm8dZLAlCu31bwvZ3UobXkH4a/8XzDpMIZyA215lwwM9M4MV7sILWUpLGx7qXXNYavwIChTlKewhtY81CSVk+67CKhi2OIEmlQBCRlyV/cnAKHbqqcabkFjoyiZRRpsgEX82QfohBXVH5fApCRGvKhKqEEJQUkEVGlMSQnl5U/p5y4egpII4fRJCcezf3S+wMMpHaGkEBkF/9+AGhdA+zEoVJhSXahnHHkZQVsk7vhSMS4UcRX6F5SO8Y4TBvh/zkeQPYQlsjThvJFgBCPT2LmidacXqQ5FUXzy5YenmzXwLMM1WkE0sboICYds5M2D6Iebs9iesC9t5T2usgsh3OvGMNfLlKHsujGAaMMn27SqwYOUrCulJCJaMmH7hhoEWVnhFNz0Jsf1CX/s5sLDUvPfp8rV9gxn9QzUx/AutJFdfZF+EaGUTgt/PSUiF+Er1Le9FCJcPGXmxQQsOvN3V9SETLargauDHBOZxF11YB3jUhPC7qGNuinhRVV7/bU0IP8CslPur4LYC19HWCHtfheASMOMd3vDi1A3vw5sQL2ZmKBHuXXhtweaP37jO4gXpKI/X4iLUkWteYRuEhLqQY6w2a7xucisNtEE4o9TvC77xzwnVKlvPzTeNLnjFNqY4HLSFKaXU42ydmpuEpFLeMqjP9Iw+tFE1qbUCtgxniAO5Vm/tREelFMC69o6GELNl1YjBBuqcBPjhxW0bPwnvPpTRW+hT2ZZaTEkFr9td+EFImonFZwKpZ/Za0srrf14JPgzY8AMzT0Q0PNpCR2Lh+c8Mow9C0CT5/thfz27F84HYko5b5dMJAVZFq6X8+hUz6uNB3aoWHTcLsZw9U+Lkzzp1JL9w0S1Y2SFESzC+JZWnNTWjv6ijudF1XWV41d7648WPB+9pSn7bolDc3Zu7hLRt/8nY+9ailVyemWFcE7KlcXdSF5tK0i3gJnF6KkjrvNX9N9I7JfW3cEa+Fyc57RTzkva6qiNcub16pzi/EeZjOpGOT3bpX1zVOuW3jl9VMN7B23/24/zsmuadICOh2zh9QspjZrlBzrKLcsVjxgrxekJCBfouY9mTyeBwnSd3yT08f2gMfTWEjlDeXNBIcZGfkoXhzJouklMuPPRe9VWm+AlTcIzNk+N236yk4HJzmSyzxXqens/pfL3IlrfLRnLhpfMeMkagmwgJ1fp7pMohOxW1psWy4g+ulPkVA2OAE16t/5viZo+tOYSL9E7Pl9TnQukJUtsSHtb5kvpyifvC8LytNoGlpn0Gzd5AQ/eNfxT1PwbTS3j+JwgHHoPpDxZde9qOQ0oMXEkHwmFX9CdBR5Lu0osQRtnI74ejMr5rZ03oYJkaQxZFNYaDtvd/uBelRQaPRVj63+3F4SFqR1j04t9cbrhVDpZVagGUoTiahF22p13yxAqNDBxBQ/sgRhjNqe6MYIptbZa2CTDn3781Uu1rSdqn+Li4E3xLKfvYASCJafJnllS+ARx5SJpW9kfO4ZaLKIEwSnd/YDKi3mYw1W779bsG34E+ETSZMPvuzqhiOKYVTpc837+4pkqFh3sSEkKTby04KqYEQ1BSXtP7V2YjZ6R4XVpS797VmYmLWvuMSDj+oioO1CBPcmL2esyhyhk9VMch9Twba/+XSNl9n4RRtGRusRN2fGLrlJzjWD4gYWHdN0qKo2PUo3OBhCQPNx8V51vnqE4PJSCyQxzESaUEu3oId/RS5GJ95N4XHSkOfkIdPZXxOF/L2pbe8NR0uvWV5OCvUMniyPwcWMs4HI/R/15LsWQn3i3FitEpLg6J15hx38Vmsm1OjpIp6OSPQ2VTvQKU01knP0pwcFaWcUWbSYhMuEAFg9bL40aUmIOcSinJ4/x0XQQq0xCwJNJslWwPORfTCrSDWpDJ4n+yzeWahcyZDl706bzaJ7fLfafi+B31FccyP5y2170pqM+jRixrNUvnT6Xj5bmPSvgl/QdY97ftGPg8hgAAAABJRU5ErkJggg==">
            </a>
        </section>
    </header>
    <div class="flex flex-row justify-between">
        <nav class="flex flex-col" style="width: 280px">
            <a href="{{ route('notes') }}" class="p-4 rounded-r-full hover:bg-gray-200 {{ setActiveLink('notes') }}">
                <svg class="icon icon-bulb mr-3" viewBox="0 0 32 32">
                    <path d="M16 0c-6.076 0-11 4.924-11 11 0 4.031 3.688 8.303 5.031 12.055 2.003 5.595 1.781 8.945 5.969 8.945 4.25 0 3.965-3.334 5.969-8.922 1.347-3.76 5.031-8.078 5.031-12.078 0-6.076-4.926-11-11-11zM18.592 27.176l-4.958 0.619c-0.177-0.512-0.367-1.111-0.598-1.893-0.003-0.010-0.007-0.021-0.009-0.031l6.188-0.773c-0.088 0.295-0.182 0.605-0.264 0.883-0.131 0.449-0.248 0.839-0.359 1.195zM12.736 24.908c-0.182-0.602-0.387-1.236-0.615-1.908h7.766c-0.123 0.359-0.246 0.719-0.352 1.059l-6.799 0.849zM16 30c-1.013 0-1.479-0.117-1.997-1.25l4.238-0.531c-0.614 1.654-1.061 1.781-2.241 1.781zM20.672 21h-9.333c-0.498-1.080-1.096-2.16-1.686-3.217-1.305-2.335-2.653-4.75-2.653-6.783 0-4.963 4.037-9 9-9s9 4.037 9 9c0 2.018-1.35 4.446-2.656 6.795-0.584 1.053-1.178 2.131-1.672 3.205zM16 5c0.275 0 0.5 0.224 0.5 0.5s-0.224 0.5-0.5 0.5c-2.757 0-5 2.243-5 5 0 0.276-0.224 0.5-0.5 0.5s-0.5-0.224-0.5-0.5c0-3.309 2.691-6 6-6z"></path>
                </svg> Notes
            </a>
            <a href="{{ route('reminders') }}" class="p-4 rounded-r-full hover:bg-gray-200 {{ setActiveLink('reminders') }}">
                <svg class="icon icon-bell mr-3" viewBox="0 0 32 32">
                    <path d="M32.047 25c0-9-8-7-8-14 0-0.58-0.056-1.076-0.158-1.498-0.526-3.532-2.88-6.366-5.93-7.23 0.027-0.123 0.041-0.251 0.041-0.382 0-1.040-0.9-1.891-2-1.891s-2 0.851-2 1.891c0 0.131 0.014 0.258 0.041 0.382-3.421 0.969-5.966 4.416-6.039 8.545-0.001 0.060-0.002 0.121-0.002 0.183 0 7-8 5-8 14 0 2.382 5.331 4.375 12.468 4.878 0.673 1.263 2.002 2.122 3.532 2.122s2.86-0.86 3.532-2.122c7.137-0.503 12.468-2.495 12.468-4.878 0-0.007-0.001-0.014-0.001-0.021l0.048 0.021zM25.82 26.691c-1.695 0.452-3.692 0.777-5.837 0.958-0.178-2.044-1.893-3.648-3.984-3.648s-3.805 1.604-3.984 3.648c-2.144-0.18-4.142-0.506-5.837-0.958-2.332-0.622-3.447-1.318-3.855-1.691 0.408-0.372 1.523-1.068 3.855-1.691 2.712-0.724 6.199-1.122 9.82-1.122s7.109 0.398 9.82 1.122c2.332 0.622 3.447 1.318 3.855 1.691-0.408 0.372-1.523 1.068-3.855 1.691z"></path>
                </svg> Reminders
            </a>
            <a href="{{ route('tag', 'tag 1') }}" class="p-4 rounded-r-full hover:bg-gray-200 {{ setActiveTagLink('tag 1') }}">
                <svg class="icon icon-price-tags mr-3" viewBox="0 0 40 32">
                    <path d="M38.5 0h-12c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l14.879-14.879c0.583-0.583 1.061-1.736 1.061-2.561v-12c0-0.825-0.675-1.5-1.5-1.5zM31 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path>
                    <path d="M4 17l17-17h-2.5c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l0.939-0.939-13-13z"></path>
                </svg> Tag 1
            </a>
            <a href="{{ route('tag', 'tag 2') }}" class="p-4 rounded-r-full hover:bg-gray-200 {{ setActiveTagLink('tag 2') }}">
                <svg class="icon icon-price-tags mr-3" viewBox="0 0 40 32">
                    <path d="M38.5 0h-12c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l14.879-14.879c0.583-0.583 1.061-1.736 1.061-2.561v-12c0-0.825-0.675-1.5-1.5-1.5zM31 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path>
                    <path d="M4 17l17-17h-2.5c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l0.939-0.939-13-13z"></path>
                </svg> Tag 2
            </a>

            <edit-labels-component :labels="['label 1','label 2']">

            </edit-labels-component>

            <a href="{{ route('archive') }}" class="p-4 rounded-r-full hover:bg-gray-200 {{ setActiveLink('archive') }}">
                <svg class="icon icon-box-add mr-3" viewBox="0 0 32 32">
                    <path d="M26 2h-20l-6 6v21c0 0.552 0.448 1 1 1h30c0.552 0 1-0.448 1-1v-21l-6-6zM16 26l-10-8h6v-6h8v6h6l-10 8zM4.828 6l2-2h18.343l2 2h-22.343z"></path>
                </svg> Archive
            </a>
            <a href="{{ route('trash') }}" class="p-4 rounded-r-full hover:bg-gray-200 {{ setActiveLink('trash') }}">
                <svg class="icon icon-bin mr-3" viewBox="0 0 32 32">
                    <path d="M4 10v20c0 1.1 0.9 2 2 2h18c1.1 0 2-0.9 2-2v-20h-22zM10 28h-2v-14h2v14zM14 28h-2v-14h2v14zM18 28h-2v-14h2v14zM22 28h-2v-14h2v14z"></path>
                    <path d="M26.5 4h-6.5v-2.5c0-0.825-0.675-1.5-1.5-1.5h-7c-0.825 0-1.5 0.675-1.5 1.5v2.5h-6.5c-0.825 0-1.5 0.675-1.5 1.5v2.5h26v-2.5c0-0.825-0.675-1.5-1.5-1.5zM18 4h-6v-1.975h6v1.975z"></path>
                </svg> Trash
            </a>
        </nav>
        <main class="flex-grow">
            @section('content')

            @show
        </main>
    </div>
</div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
