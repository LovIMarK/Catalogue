<script type="javascript" src="functions.js"></script>
<?php
	require_once('phpfunctions.inc.php');
?>


<script>
//$('#txtPageToGo').onkeypress = function(event) { alert('<?php print($strNewUrl); ?>') };
</script>

<div class="modal hide fade" id="selectPage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4>Aller &agrave; la page...</h4>
	</div>
	<div class="modal-body">
		<span>Indiquez la page &agrave; laquelle vous voulez vous rendre en entrant un nombre entre 1 et <?php print($intNbPages); ?>.</span>
		<br />
		<input type="number" name="txtPageToGo" id="txtPageToGo" min="1" max="<?php print($intNbPages); ?>" class="input-small" style="text-align:center;" value="<?php isset($_GET['page'])?print($_GET['page']):print('1'); ?>">
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Annuler</button>
		<button class="btn btn-primary" onClick="changePage('<?php print($strNewUrl); ?>', $('#txtPageToGo').value, <?php print($intNbPages); ?>);">Aller &agrave; la page</button>
	</div>
</div>