function bbcTag(open_tag, close_tag, text_id) {
	var textarea = document.getElementById(text_id);
	var text_len = textarea.value.length;
	var sel = [textarea.selectionStart, textarea.selectionEnd];
	var selected = textarea.value.substring(sel[0], sel[1]);
	var bbc = open_tag + selected + close_tag;

	textarea.value = textarea.value.substring(0, sel[0]) + bbc + textarea.value.substring(sel[1], text_len);
	textarea.selectionStart = sel[0] + open_tag.length;
	textarea.selectionEnd = sel[1] + open_tag.length;

	textarea.focus();
}


function previewPost(text_id) {
	var textarea = document.getElementById(text_id);

	$.ajax({
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('input[name="_token"]')[0].value},
		url: '/forums/post/preview',
		data: $("#"+text_id).serialize(),
		success: function(data) {
			$('.bbc-preview').html(data);
		},
		error: function() {
			$('.bbc-preview').text("An error occurred trying to preview your post. Please try again or submit a bug report.");
		},
		complete: function() {
			$('.post-preview').removeAttr("style");
		}
	});
}