<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use App\Contact;
use PhpParser\Node\Expr\AssignOp\Concat;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::select('contacts.*','c.name AS condition_name','d.name AS design_name')
            ->where('contacts.status', 1)
            ->leftJoin('conditions AS c','contacts.condition_id','=','c.id')
            ->leftJoin('designs AS d','contacts.design_id','=','d.id')
            ->orderBy('contacts.created_at','DESC')
            ->get();

        return view('index',compact('contacts'));
    }

    public function csvExport(Request $request)
    {
        $post = $request->all();
        $response = new StreamedResponse(function () use ($request, $post) {

            $stream = fopen('php://output','w');
            $contact = new Contact();

            // 文字化け回避
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');

            // ヘッダー行を追加
            fputcsv($stream, $contact->csvHeader());

            $results = $contact->getCsvData($post['start_date'], $post['end_date']);

            if(empty($results[0])) {
                fputcsv($stream, [
                    'データが存在しませんでした。',
                ]);
            } else {
                foreach ($results as $row) {
                    fputcsv($stream, $contact->csvRow($row));
                }
            }
            fclose($stream);
        });
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('content-disposition', 'attachment; filename='. $post['start_date'] . '〜' . $post['end_date'] . 'お問い合わせ一覧.csv');

        return $response;
    }

    public function getCsvData($start = null, $end = null)
    {
        // str_replace() で置換
        $start_date = str_replace('年', '-', $start); // "年"を"-"に置換する
        $start_date = str_replace('月', '-', $start_date); // "月"を"-"に置換する
        $start_date = str_replace('日', '', $start_date);  // "日"を空文字に置換する
        $end_date = str_replace('年', '-', $end); // "年"を"-"に置換する
        $end_date = str_replace('月', '-', $end_date); // "月"を"-"に置換する
        $end_date = str_replace('日', '', $end_date);  // "日"を空文字に置換する
        $data = $this->select(
                    'contacts.id',
                    'cd.name AS condition_name',
                    'd.name AS design_name',
                    'contacts.surname',
                    'contacts.name',
                    'contacts.zipcode',
                    'contacts.pref',
                    'contacts.city',
                    'contacts.street',
                    'contacts.tel_number',
                    'contacts.fax_number',
                    'contacts.email',
                    'contacts.memo'
                )
            ->leftJoin('conditions AS cd', 'contacts.condition_id','=','cd.id')
            ->leftJoin('designs AS d', 'contacts.design_id','=','d.id')
            ->where('contacts.status', 1)
            ->whereBetween('contacts.created_at', [$start_date. ' 00:00:00', $end_date. ' 23:59:59'])
            ->orderBy('contacts.created_at', 'ASC')
            ->get();

        return $data;
    }

    public function csvHeader(){
        return [
                'id',
                '建築条件',
                '建築物デザイン',
                '性',
                '名',
                'tel',
                'fax',
                '郵便番号',
                '都道府県',
                '市区町村',
                '以降の住所',
                'メールアドレス',
                'メモ'
        ];
    }

    public function csvRow($row){
        return [
            $row->id,
            $row->condition_name,
            $row->design_name,
            $row->surname,
            $row->name,
            $row->tel_number,
            $row->fax_number,
            $row->zipcode,
            $row->pref,
            $row->city,
            $row->street,
            $row->email,
            $row->memo,
        ];
    }
}
