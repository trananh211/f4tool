<!--Footer-part-->

<div class="row-fluid">
    <div id="footer" class="span12">
        <span class="span7">
            2013 &copy; Matrix Admin. Brought to you by <a href="http://themedesigner.in">Themedesigner.in</a>
        </span>
        <span class="span5">
            <div class="css-info-store">
                <span class="css-detail-store">
                    @if(Session::has('woo_store'))
                        You loggin this store:
                        {{ Session::get('woo_store.woo_name') }} |
                        {{ Session::get('woo_store.woo_link') }}
                    @endif
                </span>
            </div>
        </span>
    </div>
</div>

<!--end-Footer-part-->

