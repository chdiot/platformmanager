<?php

require_once 'Framework/Controller.php';
require_once 'Framework/Form.php';
require_once 'Modules/core/Controller/CoresecureController.php';
require_once 'Modules/core/Model/CoreStatus.php';
require_once 'Modules/antibodies/Model/AntibodiesInstall.php';
require_once 'Modules/antibodies/Model/AntibodiesTranslator.php';

/**
 * 
 * @author sprigent
 * Controller for the home page
 */
class AntibodiesconfigController extends CoresecureController {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        
        if (!$this->isUserAuthorized(CoreStatus::$USER)) {
            throw new Exception("Error 503: Permission denied");
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see Controller::indexAction()
     */
    public function indexAction($id_space) {

        $this->checkSpaceAdmin($id_space, $_SESSION["id_user"]);
        $lang = $this->getLanguage();

        $modelSpace = new CoreSpace();
        // color menu form
        $modelCoreConfig = new CoreConfig();
        $formMenuColor = $this->menuColorForm($modelCoreConfig, $id_space, $lang);
        if ($formMenuColor->check()) {
            $modelCoreConfig->setParam("antibodiesmenucolor", $this->request->getParameter("antibodiesmenucolor"), $id_space);
            $modelCoreConfig->setParam("antibodiesmenucolortxt", $this->request->getParameter("antibodiesmenucolortxt"), $id_space);
            
            $this->redirect("antibodiesconfig/".$id_space);
            return;
        }

        // maintenance form
        $formMenusactivation = $this->menusactivationForm($lang, $id_space);
        if ($formMenusactivation->check()) {

            
            $modelSpace->setSpaceMenu($id_space, "antibodies", "antibodies", "glyphicon-user", $this->request->getParameter("antibodiesmenustatus"));
            
            $this->redirect("antibodiesconfig/".$id_space);
            return;
        }

        // view
        $forms = array($formMenusactivation->getHtml($lang), $formMenuColor->getHtml($lang));
        
        $this->render(array("id_space" => $id_space, "forms" => $forms, "lang" => $lang));
    }

    protected function menusactivationForm($lang, $id_space) {

        $modelSpace = new CoreSpace();
        $statusUserMenu = $modelSpace->getSpaceMenusRole($id_space, "antibodies");
        
        $form = new Form($this->request, "menusactivationForm");
        $form->addSeparator(CoreTranslator::Activate_desactivate_menus($lang));

        $roles = $modelSpace->roles($lang);

        $form->addSelect("antibodiesmenustatus", CoreTranslator::Users($lang), $roles["names"], $roles["ids"], $statusUserMenu);
        
        $form->setValidationButton(CoreTranslator::Save($lang), "antibodiesconfig/".$id_space);
        $form->setButtonsWidth(2, 9);

        return $form;
    }

    public function menuColorForm($modelCoreConfig, $id_space, $lang){
        $menucolor = $modelCoreConfig->getParamSpace("antibodiesmenucolor", $id_space);
        $menucolortxt = $modelCoreConfig->getParamSpace("antibodiesmenucolortxt", $id_space);
        
        $form = new Form($this->request, "menuColorForm");
        $form->addSeparator(CoreTranslator::color($lang));
        $form->addColor("antibodiesmenucolor", CoreTranslator::menu_color($lang), false, $menucolor);
        $form->addColor("antibodiesmenucolortxt", CoreTranslator::text_color($lang), false, $menucolortxt);
        
        $form->setValidationButton(CoreTranslator::Save($lang), "antibodiesconfig/".$id_space);
        $form->setButtonsWidth(2, 9);
        
        return $form;
    }
}