<?php 
if (session_id () == '') {
	session_start ();
};
///////////////////////////
// gestion PDF avec HTML2PDF
///////////////////////////
	ob_start();
	$_SESSION['PDF'] = true;
	include( "requete_extraction_elements.php" );
	$content = ob_get_clean();
	require_once( "html2pdf/html2pdf.class.php");
	try
	{
		$html2pdf = new HTML2PDF("L", "A4", "fr");
		// $html2pdf->setModeDebug();
		$html2pdf->getHtmlFromPage($content);
		$html2pdf->setDefaultFont("Arial");
		$html2pdf->pdf->setDisplayMode("fullpage");
		$html2pdf->writeHTML($content);
//		$html2pdf->Output("votre_pdf.pdf");
		//Détermination d'un nom de fichier temporaire dans le répertoire courant
		$file = basename(tempnam('.', 'tmp'));
		rename($file, $file.'.pdf');
		$file = 'extraction_pdf/' . $prestation . '.pdf';
		//Sauvegarde du PDF dans le fichier
		$html2pdf->Output($file, 'F');
		//Redirection
		echo '<a href="' . $file . '" target="_blank">votre fichier pdf</a>';
		exit;
		

	}
	catch(HTML2PDF_exception $e) {
		echo $e;
		exit;
	};

// ///////////////////////////
// // Gestion PDF avec TCPDF
// ///////////////////////////

// // Include the main TCPDF library (search for installation path).
// require_once('tcpdf/tcpdf.php');

// // create new PDF document
// $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// // set document information
// $pdf->SetCreator(PDF_CREATOR);
// $pdf->SetAuthor('Nicola Asuni');
// $pdf->SetTitle('TCPDF Example 006');
// $pdf->SetSubject('TCPDF Tutorial');
// $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// // set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// // set header and footer fonts
// $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
// $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// // set default monospaced font
// $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// // set margins
// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// // set auto page breaks
// $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// // set image scale factor
// $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// // ---------------------------------------------------------

// // set font
// $pdf->SetFont('dejavusans', '', 10);

// // add a page
// $pdf->AddPage();

// // // create some HTML content
// // $html = '<h1>HTML Example</h1>
// // Some special characters: &lt; € &euro; &#8364; &amp; è &egrave; &copy; &gt; \\slash \\\\double-slash \\\\\\triple-slash
// // <h2>List</h2>
// // List example:
// // <ol>
// //     <li><img src="tcpdf/examples/images/logo_example.png" alt="test alt attribute" width="30" height="30" border="0" /> test image</li>
// //     <li><b>bold text</b></li>
// //     <li><i>italic text</i></li>
// //     <li><u>underlined text</u></li>
// //     <li><b>b<i>bi<u>biu</u>bi</i>b</b></li>
// //     <li><a href="http://www.tecnick.com" dir="ltr">link to http://www.tecnick.com</a></li>
// //     <li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.<br />Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</li>
// //     <li>SUBLIST
// //         <ol>
// //             <li>row one
// //                 <ul>
// //                     <li>sublist</li>
// //                 </ul>
// //             </li>
// //             <li>row two</li>
// //         </ol>
// //     </li>
// //     <li><b>T</b>E<i>S</i><u>T</u> <del>line through</del></li>
// //     <li><font size="+3">font + 3</font></li>
// //     <li><small>small text</small> normal <small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal</li>
// // </ol>
// // <dl>
// //     <dt>Coffee</dt>
// //     <dd>Black hot drink</dd>
// //     <dt>Milk</dt>
// //     <dd>White cold drink</dd>
// // </dl>
// // <div style="text-align:center">IMAGES<br />
// // <img src="tcpdf/examples/images/logo_example.png" alt="test alt attribute" width="100" height="100" border="0" /><img src="tcpdf/examples/images/tcpdf_box.svg" alt="test alt attribute" width="100" height="100" border="0" /><img src="tcpdf/examples/images/logo_example.jpg" alt="test alt attribute" width="100" height="100" border="0" />
// // </div>';
// $html = include( "requete_extraction_elements.php" );
// // $html = '
// // 			<table id="T_Liste_Plage" class="extraction_periode">
// // 				<tr>
// // 				<th>Plage Horaire</th>
// // 				<th>Lundi</th>
// // 				<th>Mardi</th>
// // 				<th>Mercredi</th>
// // 				<th>Jeudi</th>
// // 				<th>Vendredi</th>
// // 				<th>Samedi</th>
// // 				<th>Dimanche</th>
// // 				</tr>
// // 		<tr>
// // 							<td>lundi</td>
// // 							<td>mardi</td>
// // 							<td>mercredi</td>
// // 							<td>ggg</td>
// // 							<td>ghfghfg</td>
// // 							<td>fhhfjfhg</td>
// // 							<td>fgfghfgh</td>
// // 							<td>fhfgh</td>
// // 							</tr>
// // 		</table>
// // 		';
// // output the HTML content
// $pdf->writeHTML($html, true, false, true, false, '');
//  		//Détermination d'un nom de fichier temporaire dans le répertoire courant
//  		$file = basename(tempnam('.', 'tmp'));
//  		rename($file, $file.'.pdf');
// // 		$file = 'doc.pdf';
// $pdf->Output($file, 'F');
// //Redirection
// 		echo $html;
//  		echo '<a href="' . $file . '" target="_blank">votre fichier pdf</a>';

