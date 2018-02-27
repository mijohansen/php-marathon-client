<?php

namespace Kase;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 07/02/2018
 * Time: 15:20
 */

class MarathonClient {

    const TIMEREPORT = "timereport";
    const CLIENT = "client";
    const PROJECT = "project";
    const INVOICE = "invoice";
    const FEECODE = "feecode";
    const ORDER_NUMBER = "order_number";

    protected $marathon_server_address;
    protected $program;
    protected $password;
    protected $company_id;
    protected $internal_password;

    public function __construct($marathon_server_address, $program, $password, $internal_password, $company_id) {
        $this->marathon_server_address = $marathon_server_address;
        $this->program = $program;
        $this->password = $password;
        $this->company_id = $company_id;
        $this->internal_password = $internal_password;
    }

    public function get_endpoint_url() {
        return "https://{$this->marathon_server_address}/cgi-bin/{$this->program}?{$this->password}";
    }

    protected function __request($type, Array $request_data = [], $expected_type = null) {
        $request_data["type"] = $type;
        $request_data["password"] = $this->internal_password;
        $request_data["company_id"] = $this->company_id;
        $url = $this->get_endpoint_url();
        return MarathonUtil::fetch($url, $request_data, $expected_type);
    }

    protected function __raw_request($type, Array $request_data = []) {
        $request_data["type"] = $type;
        $request_data["password"] = $this->internal_password;
        $request_data["company_id"] = $this->company_id;
        $url = $this->get_endpoint_url();
        return MarathonUtil::raw_fetch($url, $request_data);
    }

    /**
     * Input: filter on client name Output : active clients
     * Filtering can be done on the name. The search pattern can start and/or end by an asterisk.
     * Without asterisk exact match is required. All searches are ignoring case.
     *
     * <?xml version='1.0' encoding='ISO-8859-1'?>
     * <marathon>
     *  <password>xyz</password>
     *  <type>get_clients</type>
     *  <company_id>DS</company_id>
     *  <filter_client_name>*o</filter_client_name>
     * </marathon>
     *
     * Reply:
     * <client id=”VOLV” name=”Volvo AB” internal_name=”Volvo” />
     *
     * @param $company_id
     * @param string $filter_client_name
     */
    public function get_clients($filter_client_name = null) {
        $result = $this->__request(__FUNCTION__, [
            "filter_client_name" => $filter_client_name
        ], self::CLIENT);
        return $result;
    }

    public function get_products($client_id) {

    }

    public function get_agreements($client_id) {
        return $this->__request(__FUNCTION__, [
            "client_id" => $client_id
        ]);
    }

    public function get_agreement_details($agreement_id) {
    }

    public function get_collective_mediatypes() {
    }

    public function get_mediatypes($collective_mediatype_id = null) {
        return $this->__request(__FUNCTION__, [
            "collective_mediatype_id" => $collective_mediatype_id
        ]);
    }


    public function get_discount_codes() {
    }

    public function get_surcharge_codes() {
    }

    public function get_units() {
    }

    public function get_campaign($campaign_id) {
    }

    public function get_campaigns($client_id) {
    }

    public function create_campaign($campaign_data) {
    }

    public function get_plan($plan_id) {
    }

    public function get_changed_plans($from) {
    }


    public function scratch_plan($client_id) {
    }

    public function get_order($order_number) {
        $result = $this->__raw_request(__FUNCTION__, [
            "order_number" => $order_number,
        ]);
        print_r($result);
        $order = $result["pur"]["orde"];
        $pris = $result["pur"]["orde"]["inf"]["pris"];
        $order_lines = [];
        if (isset($pris["pris-lopnr"])) {
            $order_lines[] = array_merge_recursive($order, $pris);
        } else {
            foreach ($pris as $line) {
                $order_lines[] = array_merge_recursive($order, $line);
            }
        }
        foreach ($order_lines as $i => $order_line) {
            foreach (array_keys($order_line) as $key) {
                if (is_array($order_lines[$i][$key])) {
                    unset($order_lines[$i][$key]);
                }
            }

        }
        return $order_lines;
    }

    public function get_orders($client_id = null, $media_id = null, $from_insertion_date = null, $to_insertion_date = null) {
        return $this->__request(__FUNCTION__, [
            "client_id" => $client_id,
            "media_id" => $media_id,
            "from_insertion_date" => $from_insertion_date,
            "to_insertion_date" => $to_insertion_date,
        ], self::ORDER_NUMBER);
    }

    public function create_order($client_id) {
    }

    public function change_order($client_id) {
    }

    public function change_client_status($client_id) {
    }

    public function create_order_direct($client_id) {
    }

    public function delete_order($client_id) {
    }

    public function get_invoice($client_id) {
    }

    public function get_placements($client_id) {
    }

    public function get_insertion_dates($client_id) {
    }

    public function get_sizes($client_id) {
    }

    public function get_price($client_id) {
    }

    public function get_proclients($filter_client_name = null, $timereporting = true) {
        $result = $this->__request(__FUNCTION__, [
            "filter_client_name" => $filter_client_name,
            "timereporting" => $timereporting,
        ], self::CLIENT);
        return $result;
    }

    public function get_project($client_id, $project_no, $from_date, $to_date) {
        $project = $this->__request(__FUNCTION__, [
            "client_id" => $client_id,
            "project_no" => $project_no,
            "from_date" => $from_date,
            "to_date" => $to_date
        ]);
        return $project;
    }

    public function get_projects($client_id, $timereporting = true) {
        $projects = $this->__request(__FUNCTION__, [
            "client_id" => $client_id,
            "timereporting" => $timereporting
        ], self::PROJECT);
        foreach ($projects as $i => $project) {
            $projects[$i]["key"] = $client_id . $project["id"];
        }
        return $projects;
    }

    public function create_project($client_id) {
    }

    public function get_feecodes($timereporting = true) {
        return $this->__request(__FUNCTION__, [
            "timereporting" => $timereporting,
        ], self::FEECODE);
    }

    public function get_employee($client_id) {
    }

    public function get_employees() {
        return $this->__request(__FUNCTION__);
    }

    public function get_timereports($employee_id, $from_date, $to_date) {
        return $this->__request(__FUNCTION__, [
            "employee_id" => $employee_id,
            "from_date" => $from_date,
            "to_date" => $to_date,
        ], self::TIMEREPORT);
    }

    public function create_timereport($client_id) {
    }

    public function get_proinvoice($invoice_number) {
        $invoice = $this->__request(__FUNCTION__, [
            "invoice_number" => $invoice_number,
        ]);
        return $invoice[self::INVOICE];
    }

    public function get_proinvoices($client_id, $project_no) {
        return $this->__request(__FUNCTION__, [
            "client_id" => $client_id,
            "project_no" => $project_no,
        ]);
    }

    protected function get_medias() {
    }

    public function create_plan($plan_data) {
    }
}