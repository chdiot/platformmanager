<?php
require_once 'Framework/Controller.php';
require_once 'Framework/TableView.php';
require_once 'Framework/Form.php';

require_once 'Modules/core/Controller/CoresecureController.php';
require_once 'Modules/antibodies/Model/Proto.php';

class ProtoController extends CoresecureController {

    /**
     * User model object
     */
    private $model;

    public function __construct(Request $request) {
        parent::__construct($request);
        $this->model = new Proto();
        $_SESSION["openedNav"] = "antibodies";
    }

    // affiche la liste des Prelevements
    public function indexAction($id_space) {

        // get the user list
        $protosArray = $this->model->getBySpace($id_space);

        $table = new TableView();
        $table->setTitle("Proto", 3);
        $table->addLineEditButton("protoedit/".$id_space."/");
        $table->addDeleteButton("protodelete/".$id_space."/", "id", "nom");
        
        $headers = array("id" => "ID", "nom" => "Nom");
        $tableHtml = $table->view($protosArray, $headers);
        
        $this->render(array(
            'lang' => $this->getLanguage(),
            'id_space' => $id_space,
            'tableHtml' => $tableHtml
        ));
    }

    public function editAction($id_space, $id) {

        // get isotype info
        $lang = $this->getLanguage();
        $proto = $this->model->get($id);
        
        $form = new Form($this->request, "protoeditform");
        $form->setTitle("Modifier proto");
        $form->addText("nom", "nom", true, $proto["nom"]);
        $form->setValidationButton(CoreTranslator::Save($lang), "protoedit/".$id_space.'/'.$id);
        
        if($form->check()){
            $name = $this->request->getParameter("nom");
            if ($id == 0){
                $this->model->add($name, $id_space);
            }
            else{
                $this->model->edit($id, $name, $id_space);
            }
            
            $this->redirect("proto/".$id_space);
        }

        $this->render(array(
            'lang' => $this->getLanguage(),
            'id_space' => $id_space,
            'formHtml' => $form->getHtml($lang)
        ));
    }

    public function deleteAction($id_space, $id) {

        // get source info
        $this->model->delete($id);
        $this->redirect("proto/" . $id_space);
    }

}