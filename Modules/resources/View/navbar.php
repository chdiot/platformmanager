<?php
require_once 'Modules/core/Model/CoreConfig.php';
$modelCoreConfig = new CoreConfig();
$ecmenucolor = ""; //$modelCoreConfig->getParamSpace("ecosystemmenucolor", $id_space);
$ecmenucolortxt = ""; //$modelCoreConfig->getParamSpace("ecosystemmenucolortxt", $id_space);
if ($ecmenucolor == "") {
    $ecmenucolor = "#f1f1f1";
}
if ($ecmenucolortxt == "") {
    $ecmenucolortxt = "#000";
}
?>

<head>
    <style>
        #menu-button-div a{
            font: 12px Arial;
            text-decoration: none;
            color: #333333;
            padding-left: 12px;
            /* padding: 2px 6px 2px 6px; */
        }

        #menu-button-div{
            margin-top: -2px;
            /* padding: 2px 6px 2px 6px; */
        }

        #menu-button-div:hover{
            font: 12px Arial;
            text-decoration: none;
            background-color: #e1e1e1;
            color: #333333;
            padding: 2px 2px 2px 2px;
        }

        #separatorp{
            padding-top: 12px;
            text-transform: uppercase; 
            font-weight: bold; 
            font-size: 11px;
            color: #616161;
        }
    </style>
</head>

<div class="col-md-2" style="padding: 7px; background-color: <?php echo $ecmenucolor ?>; color:<?php echo $ecmenucolortxt ?>;">

    <div class="col-md-12" style="margin-top: 0px;">

        <h4 style="text-transform: uppercase;"><?php echo ResourcesTranslator::resources($lang) ?></h4>

    </div>
    
    <div class="col-md-3 col-md-offset-9" style="margin-top: -30px;">
        <a href="resourceshelp/<?php echo $id_space ?>">
            <span class="glyphicon glyphicon-question-sign"></span>
        </a>
    </div>

    <div class="col-md-12">
    <div  class="btn-block" id="menu-button-div">
            <a id="menu-button" href="resources/<?php echo $id_space ?>"><?php echo ResourcesTranslator::Resources($lang) ?></a>
            <a id="menu-button" href="resourcesedit/<?php echo $id_space ?>/0">+</a>
        </div>
    </div>

    <div class="col-md-12">
        <p id="separatorp"><?php echo ResourcesTranslator::Sorting($lang) ?></p>

        <div  class="btn-block" id="menu-button-div">
            <a id="menu-button" href="reareas/<?php echo $id_space ?>"><?php echo ResourcesTranslator::Areas($lang) ?></a>
            <a href="reareasedit/<?php echo $id_space ?>/0">+</a> 
        </div>	
        <div  class="btn-block" id="menu-button-div">
            <a id="menu-button" href="recategories/<?php echo $id_space ?>"><?php echo ResourcesTranslator::Categories($lang) ?></a>
            <a href="recategoriesedit/<?php echo $id_space ?>/0">+</a>
        </div>
    </div>

    <div class="col-md-12">
        <p id="separatorp"><?php echo ResourcesTranslator::Responsible($lang) ?></p>

        <div  class="btn-block" id="menu-button-div">
            <a id="menu-button" href="rerespsstatus/<?php echo $id_space ?>"><?php echo ResourcesTranslator::Resps_Status($lang) ?></a>
            <a href="rerespsstatusedit/<?php echo $id_space ?>/0">+</a> 
        </div>
        <div  class="btn-block" id="menu-button-div">
            <a id="menu-button" href="resourcesvisa/<?php echo $id_space ?>"><?php echo ResourcesTranslator::Visas($lang) ?></a>
            <a href="resourceseditvisa/<?php echo $id_space ?>/0">+</a>
        </div>
    </div>

    <div class="col-md-12">
        <p id="separatorp"><?php echo ResourcesTranslator::Suivi($lang) ?></p>
        <div  class="btn-block" id="menu-button-div">
            <a id="menu-button" href="restates/<?php echo $id_space ?>"><?php echo ResourcesTranslator::States($lang) ?></a>
            <a id="menu-button" href="restatesedit/<?php echo $id_space ?>/0">+</a>
        </div>
        
        <div  class="btn-block" id="menu-button-div">
            <a id="menu-button" href="reeventtypes/<?php echo $id_space ?>"><?php echo ResourcesTranslator::Event_Types($lang) ?></a>
            <a href="reeventtypesedit/<?php echo $id_space ?>/0">+</a>      
        </div>

        <!--
        <div  class="btn-block" id="menu-button-div">
            <a id="menu-button" href="resalert/<?php echo $id_space ?>"><?php echo ResourcesTranslator::Alerts($lang) ?></a>
        </div>
        -->
        
    </div>


</div>
