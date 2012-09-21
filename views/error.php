<html>
	<head>
		<title>paste - error!</title>
		<style type="text/css">
			*, html {
				margin: 0px;
				padding: 0px;
				border: 0px;
			}
			body {
				background: #000;
				color: #fff;
				font-family: arial, helvetica, clean, sans-serif;
			}
			a, a:visited {
				color: #ccc;
				text-decoration: none;
			}
			a:hover {
				border-bottom: 1px solid #333;
			}
			.clear {
				clear: both;
			}
			#header #title {
				float: left;
				font-size: 40pt;
				font-weight: bold;
				width: 160px;
				text-align: center;
			}
			#header #msg, #content {
				margin-left: 200px;
				border-left: 1px solid #ccc;
				padding: 10px;
				font-size: 12pt;
			}
			#content {
				border-top: 1px solid #ccc;
				margin-right: 5px;
			}
			#pasteForm {
				padding: 20px;
			}
			#pasteForm select, #pasteForm input, #pasteForm textarea {
				font-size: 14pt;
				border: 1px solid #ccc;
				background: #333;
				color: #fff;
				padding: 10px;
			}
			#pasteForm textarea {
				width: 100%;
				height: 300px;
				margin-top: 10px;
			}
			#pasteForm select {
				float: right;
				margin-right: 10px;
				height: 45px;
			}
			#pasteForm input {
				float: right;
				height: 45px;
			}
			#pasteForm h1 {
				float: left;
			}
			#viewPaste {
				width: 96%;
				height: auto;
				margin-top: 10px;
				font-size: 14pt;
				border: 1px solid #ccc;
				background: #333;
				color: #fff;
				padding: 10px;
			}
			#viewPaste a, #viewPaste a:visited {
				color: #fff;
				text-decoration: none;
			}
			#viewPaste a:hover {
				border-bottom: 1px solid #ccc;
			}
			#footer {
				float: right;
				margin: 10px 10px 0px 0px;
			}
		</style>
	</head>
	<body>
		<br class="clear"/>
		<div id="header">
			<div id="title">paste</div>
			<div id="msg">paste is a simple web application for storing text online. all data stored on the sever is encrypted. and when creating your paste, you are provided with the only decryption key via sharing url. if you loose your url, you loose your decryption key. we do not store them on the server. this keeps liability down for us, and security up for you. urls are not indexed, so search engines (and hackers) cannot spider them.</div>
		</div>
		<br class="clear"/>
		<div id="content">
			<h1>Error!</h1>
			<div id="viewPaste"><? echo $msg; ?></div>
			<form name="pasteForm" id="pasteForm" method="post" action="?/new/">
				<input type="submit" name="button" id="button" value="new" />
			</form>
		</div>
		<br/><br class="clear"/>
		<div id="footer">download the code:&nbsp;<a href="http://code.xero.nu/paste">xero.nu</a>&nbsp;/&nbsp;<a href="https://github.com/fontvirus/paste">github</a></div>
	</body>
</html>