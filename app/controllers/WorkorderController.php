<?php
require_once __DIR__ . '/../models/WorkOrder.php';
require_once __DIR__ . '/../config/database.php';

class WorkorderController {
    public function syncWorkOrders() {
        try {
            $cnx = getConnection();
            $model = new WorkOrder($cnx);

            $url = "https://dev299646.service-now.com/api/now/table/wm_order";
            $username = "admin";
            $password = "N9@uSi@WW5za";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);

            $response = curl_exec($ch);
            if ($response === false) {
                throw new Exception("Erreur cURL : " . curl_error($ch));
            }
            curl_close($ch);

            // Debug : écrire la réponse brute dans un fichier
            file_put_contents(__DIR__ . '/../../debug_api_response.txt', $response);

            $data = json_decode($response, true);
            if (!isset($data['result'])) {
                throw new Exception("Format de réponse invalide");
            }

            // 1. Récupérer tous les numéros de work orders ServiceNow
            $serviceNowNumbers = [];
            foreach ($data['result'] as $wo) {
                if (isset($wo['number'])) {
                    $serviceNowNumbers[] = $wo['number'];
                }
            }

            // 2. Récupérer tous les numéros de work orders en BDD
            $bddNumbers = $model->getAllNumbers();

            // 3. Supprimer ceux qui ne sont plus dans ServiceNow
            $toDelete = array_diff($bddNumbers, $serviceNowNumbers);
            foreach ($toDelete as $num) {
                $model->deleteByNumber($num);
            }

            // 4. Insérer ou mettre à jour les work orders reçus
            $count = 0;
            foreach ($data['result'] as $wo) {
                if (!isset($wo['number']) || !isset($wo['u_client'])) {
                    continue;
                }
                $numero = $wo['number'];
                $client = $wo['u_client'] ?? '';
                $technology = $wo['u_technologie'] ?? '';
                $offre = $wo['u_offre'] ?? '';
                $status = $wo['state'] ?? '1';
                $date = $wo['opened_at'] ?? date('Y-m-d H:i:s');
                $model->save($numero, $client, $technology, $offre, $status, $date);
                $count++;
            }

            return [
                'success' => true,
                'message' => "$count Work Orders synchronisés avec succès."
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Erreur lors de la synchronisation : " . $e->getMessage()
            ];
        }
    }

    public function index() {
        require __DIR__ . '/../views/all_workorders.php';
    }

    public function detail($id) {
        $cnx = getConnection();
        $model = new WorkOrder($cnx);
        $workorder = $model->getById($id);
        require __DIR__ . '/../views/workorder_detail.php';
    }
}
