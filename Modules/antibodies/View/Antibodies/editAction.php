<?php include 'Modules/antibodies/View/layout.php' ?>

<!-- body -->     
<?php startblock('content') ?>

<div class="col-md-10" id="pm-content">
    
    <div class="col-md-12" id="pm-form">
        <?php if(isset($_SESSION["message"])){
        ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION["message"] ?>
        </div>
        <?php
            unset($_SESSION["message"]);
        } ?>
        
        
        <?php echo $form ?>
    </div>
    <div class="col-md-12" id="pm-content">
        <div class="col-md-12" id="pm-table">
            <?php echo $tissusTable ?>
            <a class="btn btn-primary" id="addtissusbutton"><?php echo AntibodiesTranslator::addTissus($lang) ?></a>
        </div>
        <div class="col-md-12" id="pm-table">
            <?php echo $ownersTable ?>
            <a class="btn btn-primary" id="addownerbutton"><?php echo AntibodiesTranslator::addOwner($lang) ?></a>
        
        </div>
    </div>
    
</div>

<!--  *************  -->
<!--  Popup windows  -->
<!--  *************  -->
<link rel="stylesheet" type="text/css" href="Framework/pm_popup.css">
<div id="hider" class="col-xs-12"></div> 
<div id="tissuspopup_box" class="pm_popup_box" style="display: none;">
    <div class="col-md-1 col-md-offset-11" style="text-align: right;"><a id="tissusbuttonclose" class="glyphicon glyphicon-remove" style="cursor:pointer;"></a></div>
        <?php echo $formtissus ?>
</div> 
<div id="ownerpopup_box" class="pm_popup_box" style="display: none;">
    <div class="col-md-1 col-md-offset-11" style="text-align: right;"><a id="ownerbuttonclose" class="glyphicon glyphicon-remove" style="cursor:pointer;"></a></div>
        <?php echo $formowner ?>
</div> 

<?php include 'Modules/antibodies/View/Antibodies/editscript.php';  ?>

<?php endblock();
