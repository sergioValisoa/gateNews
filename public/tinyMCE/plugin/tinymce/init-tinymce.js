tinymce.init({
	selector: "textarea",
	width: "100%",
	height: 500,
	plugins: [
	"advlist autolink link image lists charmap print preview hr anchor pagebreak",
	"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	"save contextmenu table  directionality emoticons template paste textcolor"
	],
	toolbar:"insert file undo redo | styleselect | bold italic | alignleft aligncenter alignright align justify |bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"

});