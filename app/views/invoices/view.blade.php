@extends('header')

@section('head')
	@parent

		<script type="text/javascript" src="{{ asset('js/pdf_viewer.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/compatibility.js') }}"></script>
@stop

@section('content')

	<div class="pull-right">
		{{ Button::normal('Download PDF', array('onclick' => 'onDownloadClick()', 'class' => 'btn-lg')) }}
		@if ($invoice->client->account->isGatewayConfigured())
			{{ Button::primary_link(URL::to('payment/' . $invitation->invitation_key), 'Pay Now', array('class' => 'btn-lg pull-right')) }}
		@endif
	</div>
	<div class="clearfix"></div><p>&nbsp;</p>

	<iframe id="theFrame" frameborder="1" width="100%" height="650" style="display:none;margin: 0 auto"></iframe>
	<canvas id="theCanvas" style="display:none;width:100%;border:solid 1px #CCCCCC;"></canvas>

	<script type="text/javascript">

		$(function() {
			window.invoice = {{ $invoice->toJson() }};
			@if (file_exists($invoice->client->account->getLogoPath()))
				invoice.image = "{{ HTML::image_data($invoice->client->account->getLogoPath()) }}";
				invoice.imageWidth = {{ $invoice->client->account->getLogoWidth() }};
				invoice.imageHeight = {{ $invoice->client->account->getLogoHeight() }};
			@endif
			var doc = generatePDF(invoice);
			var string = doc.output('datauristring');
			alert(isFirefox);
			alert(isChrome);
			if (isFirefox || isChrome) {
				$('#theFrame').attr('src', string).show();
			} else {
				alert(1);
				var pdfAsArray = convertDataURIToBinary(string);	
			    PDFJS.getDocument(pdfAsArray).then(function getPdfHelloWorld(pdf) {

			      pdf.getPage(1).then(function getPageHelloWorld(page) {
			        var scale = 1.5;
			        var viewport = page.getViewport(scale);

			        var canvas = document.getElementById('theCanvas');
			        var context = canvas.getContext('2d');
			        canvas.height = viewport.height;
			        canvas.width = viewport.width;

			        page.render({canvasContext: context, viewport: viewport});
			        $('#theCanvas').show();
			      });
			    });				
			 }
		});

		function onDownloadClick() {
			var doc = generatePDF(invoice);
			doc.save('Invoice-' + invoice.invoice_number + '.pdf');
		}


	</script>

@stop