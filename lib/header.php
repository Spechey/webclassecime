<?php
header('Content-type: text/html; charset=ansi');
?>
<script src="js/jquery-3.4.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
function cBck_update(oid,action,actionid,)
{
	$.get( "api.php",
		   {"action":action,"actionid":actionid},
		   function( data ) {
				if (data.data.isValide)
				{
					$( "."+oid+" " ).removeClass("notValide");
					$( "."+oid+" " ).addClass("isValide");
					$( "."+oid+" a" ).html("-");
					$( "."+oid+" a" ).attr("onclick","cBck_update('"+oid+"','cBckDel','"+actionid+"')");
				}
				else
				{
					$( "."+oid+" " ).addClass("notValide");
					$( "."+oid+" " ).removeClass("isValide");
					$( "."+oid+" a" ).html("+");
					$( "."+oid+" a" ).attr("onclick","cBck_update('"+oid+"','cBckAdd','"+actionid+"')");
				}
		   }
		);
}
</script>
<style type="text/css">
body {
	text-align: center;
}
table , th, td {
	border: 1px solid black;
	border-collapse: collapse;
	text-align: center;
	padding: 3px;
}
table { margin: auto;}
tr:nth-child(even) {background: #CCC}
tr:nth-child(odd) {background: #FFF}
a {
			text-decoration: none;
			color: darkblue;
}
.active, .active a{
	background: #111;
	color: #FFF;
}
/* listes d'evenement */
div.headerActions {
    /*display: flex;
    justify-content: flex-start;*/
	display: inline-block;
	margin: 4px;
	padding: 0 15px 0 0;
}
div.headerActions:nth-child(even) {background: #FEF}
div.headerActions:nth-child(odd) {background: #EEF}

div.headerActions > *{
    display: inline-block;
	margin:  5px 1px;
	padding: 0;
}
div.headerActions h2 {
	width: 150px;
}
ul.evt li, ul.epr li,ul.mch li , ul.filter ul li {
   display: inline-block;
   text-align: center;
   padding: 5px ;
   margin:  0px;
   border: 1px solid black;
}
ul.filter {
   padding:3px;
   margin: 5px 10px 20px 0;
}
ul.filter ul {
   padding: 0px;
   margin:  0px;
}
ul.filter > li {
	display: block;
	text-align: left;
	margin:  0px;
}
/* msg */
div.msg {
		background-color: #FAA;
		text-align: center;
		font-size: 1.4em;
		margin: 0 auto 10px auto;
}


/* bloc */
table.bck {
	width: 3em;
	text-align:center;
	margin: auto;
	table-layout: fixed;
}
table.bck td {
	border: none;
    
    width: 30px;
}

table.bck .id{
	font-weight : bold;
}
table.bck .pts{
	font-size: 0.5em;
}
table.bck .action a{
	font-weight : bold;
	text-decoration: none;
	font-size: 2em;
	display: block;
}
 table.resultats .action a{
	font-weight : bold;
	text-decoration: none;
	font-size: 1em;
	display: block;
}
table.bck td.isValide:nth-child(even), table.resultats  td.isValide:nth-child(even) {
	background-color: #0F7A;
}
table.bck td.isValide:nth-child(odd), table.resultats  td.isValide:nth-child(odd) {
	background-color: #0F85;
}

table.bck td.notValide:nth-child(odd) {
	background-color: #F005;
}



th.pts {
	font-weight : normal;
	font-size: small;
	background-color: #EEF;
}
td.btnSaisie {
	width: 2em;
}
th.admin-block-result i{
	font-size: 2em;
}
</style>