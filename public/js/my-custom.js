$(document).ready(function(){

	$('.js-checkShopify').click(function () {
		var domain = $('#js-domain').val();
		var page = $('#js-page').val();

        var redirectWindow = window.open('shopify-give-content/'+domain+'/'+page, '_blank');
        redirectWindow.location;
    });

});
