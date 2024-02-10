<?php

$url = 'http://172.31.1.4/ceylincolifeinsurance/';
// $url = 'http://172.31.1.3/ceylincolifeinsurance_uat/';

$fp = fopen('php://input', 'r');
$rawData = json_decode(stream_get_contents($fp));
// file_put_contents('filename.txt', print_r('Noresha', true));
$fp = fopen('filename_'.date('Y_m_d').'.log', 'a');
fwrite($fp, date('Y-m-d H:i:s') . " : Request=>" . json_encode($rawData, JSON_PRETTY_PRINT)."\n");
file_put_contents('filename.txt', print_r($rawData, true));


// print_r($rawData);
if ($_REQUEST['chataction'] == 'createTicket') {
    $uploadedfile = explode("base64,", $rawData->chatuploadfile);

    $url = $url.'TicketwithAttachment.php';
    $postfields = array(
        'policy_no' => $rawData->chatpolicyno,
        'ticket_type' => $rawData->chatticket_type,
        'sub_type' => $rawData->chatsub_type,
        // 'uploaded_file' => $rawData->chatuploadfile,
        'uploaded_file' => $uploadedfile['1'],
        'chatfilename' => $rawData->chatfilename,
        );
} elseif ($_REQUEST['chataction'] == 'attachmentPolicy') {
    $url = $url."attachment_policy.php";
    $postfields = array(
        'policy_no' => $rawData->policy_no,
        'nic_no' => $rawData->nic_no,
        'mobile_no' => $rawData->mobile_no,
        'filename' => $rawData->filename,
        'file_contents' => $rawData->file_contents,
        'branch' => $rawData->branch,
        );
} elseif ($_REQUEST['chataction'] == 'createTicketwithWhatsapp') {
    $url = $url.'TicketwithAttachmentWhatsApp.php';
    $postfields = array(
        'policy_no' => $rawData->chatpolicyno,
        'ticket_type' => $rawData->chatticket_type,
        'sub_type' => $rawData->chatsub_type,
        'uploaded_file' => $rawData->chatuploadfile,
        'chatfilename' => $rawData->chatfilename,
        );
} elseif ($_REQUEST['chataction'] == 'attachmentPolicywithWhatsapp') {
    $url = $url."attachment_policyWhatsApp.php";
    $postfields = array(
        'policy_no' => $rawData->policy_no,
        'nic_no' => $rawData->nic_no,
        'mobile_no' => $rawData->mobile_no,
        'filename' => $rawData->filename,
        'file_contents' => $rawData->file_contents,
        'branch' => $rawData->branch,
        );
}
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);

curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));
$json = curl_exec($curl);
curl_close($curl);


print_r($json);

exit;
