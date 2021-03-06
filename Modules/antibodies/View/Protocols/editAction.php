<?php include 'Modules/antibodies/View/layout.php' ?>

<!-- body -->     
<?php startblock('content') ?>
<div class="col-md-12 pm-table">
	<div class="col-md-6 col-md-offset-3">
	<form role="form" class="form-horizontal" action="protocolseditquery/<?php echo $id_space ?>"
		method="post">
	
	
		<div class="page-header">
			<h1>
				<?php if($protocol['id'] != ""){ ?>
					Editer protocole <br> <small></small>
				<?php 
				} else {
				?>	
					Ajouter protocole <br> <small></small>
				<?php } ?>
			</h1>
		</div>
	
		<?php if($protocol['id'] != ""){ ?>
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">Id</label>
			<div class="col-xs-10">
				<input class="form-control" id="id" type="text" name="id" readonly
				       value="<?php echo  $protocol['id'] ?>"  
				/>
			</div>
		</div>
		<?php } ?>
						
	
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">KIT</label>
			<div class="col-xs-10">
				<select class="form-control" name="kit">
					<?php 
					foreach ($kits as $kit){
						$kitId = $kit["id"];
						$kitName = $kit["nom"];
						$selected = "";
						if ($protocol["kit"] == $kitId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $kitId ?>" <?php echo  $selected ?>> <?php echo  $kitName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
	
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">No Proto</label>
			<div class="col-xs-10">
				<input class="form-control" id="no_proto" type="text" name="no_proto"
				       value="<?php echo  $this->clean ( $protocol ['no_proto'] ); ?>"  
				/>
			</div>
		</div>
	
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">Proto</label>
			<div class="col-xs-10">
				<select class="form-control" name="proto">
					<?php 
					foreach ($protos as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["proto"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">Fixative</label>
			<div class="col-xs-10">
				<select class="form-control" name="fixative">
					<?php 
					foreach ($fixatives as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["fixative"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">Option</label>
			<div class="col-xs-10">
				<select class="form-control" name="option">
					<?php 
					foreach ($options as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["option_"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">enzyme</label>
			<div class="col-xs-10">
				<select class="form-control" name="enzyme">
					<?php 
					foreach ($enzymes as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["enzyme"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">dém</label>
			<div class="col-xs-10">
				<select class="form-control" name="dem">
					<?php 
					foreach ($dems as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["dem"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">AcI Inc</label>
			<div class="col-xs-10">
				<select class="form-control" name="acl_inc">
					<?php 
					foreach ($aciincs as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["acl_inc"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">Linker</label>
			<div class="col-xs-10">
				<select class="form-control" name="linker">
					<?php 
					foreach ($linkers as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["linker"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">Linker Inc</label>
			<div class="col-xs-10">
				<select class="form-control" name="inc">
					<?php 
					foreach ($incs as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["inc"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">AcII</label>
			<div class="col-xs-10">
				<select class="form-control" name="acll">
					<?php 
					foreach ($aciis as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["acll"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">Inc</label>
			<div class="col-xs-10">
			
				<select class="form-control" name="inc2">
					<?php 
					foreach ($incs as $var){
						$varId = $var["id"];
						$varName = $var["nom"];
						$selected = "";
						if ($protocol["inc2"] == $varId){
							$selected = "selected=\"selected\"";
						}
						?>
						<OPTION value="<?php echo  $varId ?>" <?php echo  $selected ?>> <?php echo  $varName ?> </OPTION>
					<?php 
					}	
					?>
				</select>
			</div>
		</div>	
		
		<div class="form-group">
			<label for="inputEmail" class="control-label col-xs-2">Est associé</label>
			<div class="col-xs-10">
				<select class="form-control" name="associate">
					
					<OPTION value="1" <?php if($protocol ['associe'] == 1){echo "selected=\"selected\"";}?>> Associé </OPTION>
					<OPTION value="0" <?php if($protocol ['associe'] == 0){echo "selected=\"selected\"";}?>> Général </OPTION>
				</select>
			</div>
		</div>			    

		<br></br>		
		<div class="col-xs-6 col-xs-offset-6" id="button-div">
		        <input type="submit" class="btn btn-primary" value="Save" />
		        <?php if($protocol['id'] != ""){ ?>
		        	<button type="button" onclick="location.href='<?php echo "protocolsdelete/".$id_space."/".$protocol['id'] ?>'" class="btn btn-danger"><?php echo  CoreTranslator::Delete($lang)?></button>
				<?php }?>
				<button type="button" onclick="location.href='protocols'" class="btn btn-default">Cancel</button>
		</div>
      </form>
	</div>
</div>

<?php endblock();
