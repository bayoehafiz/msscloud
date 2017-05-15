jQuery(document).ready(function() {
	jQuery('body').on('click','.nrghost-post-like',function(event){
		event.preventDefault();
		heart = jQuery(this);
		post_id = heart.data("post_id");
		heart.addClass('in-process');
		jQuery.ajax({
			type: "post",
			url: ajax_var.url,
			data: "action=nrghost-post-like&nonce="+ajax_var.nonce+"&nrghost_post_like=&post_id="+post_id,
			success: function(count){
				if( count.indexOf( "already" ) !== -1 )
				{
					var lecount = count.replace("already","");
					if (lecount === "0")
					{
						lecount = "0";
					}
					heart.siblings('span.counter').prop('title', 'Like it :)');
					heart.removeClass("liked in-process");
					heart.siblings('span.counter').html( '<span class="counter">' + lecount + '</span>');
				}
				else
				{
					heart.siblings('span.counter').prop('title', 'Unlike it :(');
					heart.removeClass('in-process').addClass("liked");
					heart.siblings('span.counter').html( '<span class="counter">' + count + '</span>');
				}
			}
		});
	});
});