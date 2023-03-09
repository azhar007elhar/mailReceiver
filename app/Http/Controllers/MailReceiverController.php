<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Facades\Client;

class MailReceiverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            /* Alternative by using the Facade */
            // $oClient = \Webklex\IMAP\Facades\Client::account('default');
            // $oClient = \Webklex\IMAP\Facades\Client::account('default');

            $options = [
                'host'          => 'mail.crmdermafric.com',
                'port'          => 993,
                'encryption'    => 'ssl',
                'validate_cert' => true,
                'username'      => 'support@crmdermafric.com',
                'password'      => 'phTMhc@I=^uH',
                'protocol'      => 'imap'
            ];
            $oClient = Client::make($options);

            //Connect to the IMAP Server
            $oClient->connect();


            //Get all Mailboxes
            /** @var \Webklex\IMAP\Support\FolderCollection $aFolder */
            $aFolder = $oClient->getFolders();

            //Loop through every Mailbox
            /** @var \Webklex\IMAP\Folder $oFolder */
            $results = [];
            foreach ($aFolder as $oFolder) {

                //Get all Messages of the current Mailbox $oFolder
                /** @var \Webklex\IMAP\Support\MessageCollection $aMessage */
                // $aMessage = $oFolder->messages()->all()->get();

                // get unread messages 
                // $aMessage = $oFolder->search()->unseen()->get();

                // get all messages 
                $aMessage = $oFolder->search()->all()->get();

                // get all messages with limite 
                // $aMessage = $oFolder->search()->all()->limit(10)->get();


                /** @var \Webklex\IMAP\Message $oMessage */
                foreach ($aMessage as $oMessage) {
                    $res = (object) [
                        'uid' => $oMessage->uid,
                        'Subject' =>  mb_decode_mimeheader($oMessage->getSubject()),
                        'Attachments Count' =>  $oMessage->getAttachments()->count(),
                        'Attachments' =>  $oMessage->getAttachments(),
                        'HTML Body' =>  $oMessage->getHTMLBody(true),
                        'body' => mb_decode_mimeheader($oMessage->getTextBody()),
                        'From' => $oMessage->getFrom()[0]->mail,
                    ];
                    array_push($results, $res);
                    // echo $oMessage->getSubject() . '<br />';
                    // echo 'Attachments: ' . $oMessage->getAttachments()->count() . '<br />';
                    // echo $oMessage->getHTMLBody(true);

                    //Move the current Message to 'INBOX.read'
                    // if ($oMessage->moveToFolder('INBOX.read') == true) {
                    //     echo 'Message has ben moved';
                    // } else {
                    //     echo 'Message could not be moved';
                    // }
                }
            }

            // reverse emails to desc 
            return array_reverse($results);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()] , 401);
            // return $th->getMessage();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
