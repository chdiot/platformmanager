<?php

require_once 'Framework/Controller.php';
require_once 'Framework/Form.php';
require_once 'Framework/TableView.php';
require_once 'Modules/core/Controller/CoresecureController.php';
require_once 'Modules/services/Model/ServicesTranslator.php';
require_once 'Modules/services/Model/SeService.php';
require_once 'Modules/services/Model/SeServiceType.php';
require_once 'Modules/services/Model/SeOrder.php';
require_once 'Modules/ecosystem/Model/EcUser.php';

/**
 * 
 * @author sprigent
 * Controller for the home page
 */
class ServicesordersController extends CoresecureController {

    private $serviceModel;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        //$this->checkAuthorizationMenu("services");
    }

    /**
     * (non-PHPdoc)
     * @see Controller::indexAction()
     */
    public function indexAction($id_space, $status = "") {

        $this->checkAuthorizationMenuSpace("services", $id_space, $_SESSION["id_user"]);
        $lang = $this->getLanguage();

        // get sort action
        $sortentry = "id";

        // get the commands list
        $modelEntry = new SeOrder();
        $entriesArray = array();
        if ($status == "") {
            if (isset($_SESSION["supplies_lastvisited"])) {
                $status = $_SESSION["supplies_lastvisited"];
            } else {
                $status = "all";
            }
        }

        if ($status == "all") {
            $entriesArray = $modelEntry->entries($sortentry);
        } else if ($status == "opened") {
            $entriesArray = $modelEntry->openedEntries($sortentry);
        } else if ($status == "closed") {
            $entriesArray = $modelEntry->closedEntries($sortentry);
        }

        $table = new TableView();
        $table->setTitle(ServicesTranslator::Services_Orders($lang));
        $table->addLineEditButton("servicesorderedit/" . $id_space);
        $table->addDeleteButton("servicesorderdelete/" . $id_space, "id", "id");

        $headersArray = array(
            "no_identification" => ServicesTranslator::No_identification($lang),
            "user_name" => CoreTranslator::User($lang),
            "id_status" => CoreTranslator::Status($lang),
            "date_open" => ServicesTranslator::Opened_date($lang),
            "date_close" => ServicesTranslator::Closed_date($lang),
            "date_last_modified" => ServicesTranslator::Last_modified_date($lang),
        );


        for ($i = 0; $i < count($entriesArray); $i++) {
            if ($entriesArray[$i]["id_status"]) {
                $entriesArray[$i]["id_status"] = ServicesTranslator::Opened($lang);
            } else {
                $entriesArray[$i]["id_status"] = ServicesTranslator::Closed($lang);
            }
            $entriesArray[$i]["date_open"] = CoreTranslator::dateFromEn($entriesArray[$i]["date_open"], $lang);
            $entriesArray[$i]["date_close"] = CoreTranslator::dateFromEn($entriesArray[$i]["date_close"], $lang);
            $entriesArray[$i]["date_last_modified"] = CoreTranslator::dateFromEn($entriesArray[$i]["date_last_modified"], $lang);
        }
        $tableHtml = $table->view($entriesArray, $headersArray);

        if ($table->isPrint()) {
            echo $tableHtml;
            return;
        }

        // 
        $this->render(array(
            'lang' => $lang,
            'id_space' => $id_space,
            'tableHtml' => $tableHtml
                ), "indexAction");
    }

    public function openedAction($id_space) {
        $_SESSION["supplies_lastvisited"] = "opened";
        $this->indexAction($id_space, "opened");
    }

    public function closedAction($id_space) {
        $_SESSION["supplies_lastvisited"] = "closed";
        $this->indexAction($id_space, "closed");
    }

    public function AllAction($id_space) {

        $_SESSION["supplies_lastvisited"] = "all";
        $this->indexAction($id_space, "all");
    }

    public function deleteAction($id_space, $id) {
        $this->checkAuthorizationMenuSpace("services", $id_space, $_SESSION["id_user"]);

        $this->serviceModel->delete($id);
        $this->redirect("services/" . $id_space);
    }

    public function editAction($id_space, $id) {
        $this->checkAuthorizationMenuSpace("services", $id_space, $_SESSION["id_user"]);
        $lang = $this->getLanguage();

        $form = new Form($this->request, "orderEditForm");
        $form->setTitle(ServicesTranslator::Edit_order($lang));

        $modelOrder = new SeOrder();

        if ($id > 0) {
            $value = $modelOrder->getEntry($id);
            $items = $modelOrder->getOrderServices($id);
        } else {
            $value = $modelOrder->defaultEntryValues();
            $items = array("services" => array(), "quantities" => array());
        }

        $modelUser = new EcUser();
        $users = $modelUser->getAcivesForSelect("name");

        $form->addSeparator(CoreTranslator::Description($lang));
        $form->addText("no_identification", ServicesTranslator::No_identification($lang), false, $value["no_identification"]);
        $form->addSelect("id_user", CoreTranslator::User($lang), $users["names"], $users["ids"], $value["id_user"]);
        $form->addSelect("id_status", CoreTranslator::Status($lang), array(CoreTranslator::Open($lang), CoreTranslator::Close($lang)), array(1, 0), $value["id_status"]);

        $form->addText("date_open", ServicesTranslator::Opened_date($lang), false, CoreTranslator::dateFromEn($value["date_open"], $lang), false);
        $form->addText("date_close", ServicesTranslator::Closed_date($lang), false, CoreTranslator::dateFromEn($value["date_close"], $lang), false);
        $form->addText("date_last_modified", ServicesTranslator::Last_modified_date($lang), false, CoreTranslator::dateFromEn($value["date_last_modified"], $lang), false);

        $modelServices = new SeService();
        $services = $modelServices->getForList($id_space);

        $formAdd = new FormAdd($this->request, "orderEditForm");
        $formAdd->addSelect("services", ServicesTranslator::services($lang), $services["names"], $services["ids"], $items["services"]);
        $formAdd->addNumber("quantities", ServicesTranslator::Quantity($lang), $items["quantities"]);
        $formAdd->setButtonsNames(CoreTranslator::Add($lang), CoreTranslator::Delete($lang));
        $form->addSeparator(ServicesTranslator::Services_list($lang));
        $form->setFormAdd($formAdd);

        $form->setValidationButton(CoreTranslator::Save($lang), "servicesorderedit/" . $id_space . "/" . $id);
        $form->setButtonsWidth(2, 10);

        if ($form->check()) {

            $id_order = $modelOrder->setOrder($id, $id_space, $this->request->getParameter("id_user"), $this->request->getParameter("no_identification"), $this->request->getParameter("id_status"), CoreTranslator::dateToEn($this->request->getParameter("date_open"), $lang), CoreTranslator::dateToEn($this->request->getParameter("date_last_modified"), $lang), CoreTranslator::dateToEn($this->request->getParameter("date_close"), $lang));

            $servicesIds = $this->request->getParameter("services");
            $servicesQuantities = $this->request->getParameter("quantities");

            for ($i = 0; $i < count($servicesQuantities); $i++) {
                if ($id == 0) {
                    $qOld = 0;
                } else {
                    $qOld = $modelOrder->getOrderServiceQuantity($id, $servicesIds[$i]);
                }
                $qDelta = $servicesQuantities[$i] - $qOld[0];
                $modelServices->editquantity($servicesIds[$i], $qDelta, "subtract");
                $modelOrder->setService($id_order, $servicesIds[$i], $servicesQuantities[$i]);
            }

            $this->redirect("servicesorders/" . $id_space);
            return;
        }

        $this->render(array("id_space" => $id_space, "lang" => $lang, "formHtml" => $form->getHtml($lang)));
    }

}