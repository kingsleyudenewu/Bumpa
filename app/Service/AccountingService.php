<?php

namespace App\Service;

use App\Models\Book;
use App\Models\BookSummary;
use App\Models\Tx;
use App\Traits\HasApiResponse;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountingService
{
    public function postLedger(array $transactionLegs)
    {
        $txID = addslashes($transactionLegs[0]['tx']); //transaction code from first params
        if (empty($txID)) $txID = (string) Str::uuid();
        $saved_book_ids = $saved_coa_ids = [];

        $getTx = Tx::where('tx_code', $txID)->get();
        if ($getTx->count() > 0) {
            foreach ($getTx as $delTx) {
                $delTx->delete();
            }
        }

        DB::beginTransaction();
        try {
            $success = true;
            $bookInfo = [];

            foreach ($transactionLegs as $params) {
                if ($params['amount'] == 0 || $params['book_id'] < 1 || empty($params['memo'])) return false;

                (empty($params['value_date'])) ? $params['tx_value_date'] = date('Y-m-d') : $params['tx_value_date'] = date('Y-m-d', strtotime($params['value_date']));
                (empty($params['payment_date'])) ? $params['tx_payment_date'] = date('Y-m-d') : $params['tx_payment_date'] = date('Y-m-d', strtotime($params['payment_date']));

                $txLedger = new Tx;
                $txLedger->tx_book_id = stripslashes($params['book_id']);
                $txLedger->tx_date = $params['payment_date'];
                $txLedger->tx_value_date = $params['value_date'];
                $txLedger->tx_amount = $params['amount'];
                $txLedger->tx_remarks = $params['memo'];
                $txLedger->tx_code = $txID;

                if ($txLedger->save()) {
                    $saved_book_ids[] = $params['book_id'];
                    $bookInfo[] = [$params['book_id'], $txID, $params['amount'], $params['payment_date'], $params['value_date']];
                } else {
                    $success = false;
                }
            }

            if ($success === true) {
                $this->updateBookBalance($bookInfo);
                DB::commit();
                return true;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (Exception $exception) {
            DB::rollBack();
            logger($exception->getMessage());
            return false;
        }
    }

    /**
     * @param array $bookids
     */
    private function updateBookBalance(array $bookids)
    {
        foreach ($bookids as $info) {

            $book_id = $info[0];
            $amount = $info[2];
            $paymentDate = $info[4];

            $book = Book::where('book_id', $book_id)->first();
            $bookType = $book->book_type;

            $acceptable_types = [];
            $acceptable_types[] = 'CUSTOMER';

            if (!in_array($bookType, $acceptable_types)) {
                continue;
            }

            $this->processBookSummary($paymentDate, $book_id, $amount, $book);
        }
    }

    private function processBookSummary(string $paymentDate, int $book_id, float $amount, Book $book)
    {
        $success = [];

        $bookSummary = BookSummary::where([
            'bs_date' => $paymentDate,
            'bs_book_id' => $book_id
        ])->lockForUpdate()
            ->first();

        if ($bookSummary) {
            $bookSummary->bs_balance = $bookSummary->bs_balance + $amount;
            $bookSummary->save() ? $success[$book_id] = true : $success[$book_id] = false;
        } else {
            BookSummary::create([
                'bs_book_id' => $book->book_id,
                'bs_balance' => $amount,
                'bs_date' => $paymentDate
            ]);

            $success[$book_id] = true;
        }
    }

}
