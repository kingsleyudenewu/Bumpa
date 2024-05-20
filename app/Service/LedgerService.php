<?php

namespace App\Service;

use App\Enums\BookEnum;
use App\Enums\LedgerEnum;
use App\Models\Book;
use App\Models\Ledger;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LedgerService
{
    /**
     * deductBankTransfer
     *
     * @param  mixed $userId
     * @param  mixed $deductionAmount
     * @return void
     */
    public function deductBankTransfer(int $userId, int $deductionAmount, $reversal = false)
    {
        $withdrawalLedgerId = Ledger::where(['ledger_name' => LedgerEnum::WITHDRAWAL])->value('ledger_id');

        $iWithdrawalBookId = Book::where([
            'book_src_id' => $withdrawalLedgerId,
            'book_type' => BookEnum::LEDGER
        ])
            ->value('book_id');

        $customerBookId = Book::where([
            'book_src_id' => $userId,
            'book_type' => BookEnum::CUSTOMER
        ])
            ->value('book_id');

        //Then we deduct amount from customer wallet and credit withdrawal ledger wallet
        $allTX = $this->setLedgerVariables($deductionAmount, $customerBookId, $iWithdrawalBookId, 'Withdrawal deduction of '.$deductionAmount, $reversal);

        //post ledger
        return resolve(AccountingService::class)->postLedger($allTX);
    }

    /**
     * Perform wallet to wallet transfer between customers
     *
     * @param User $fromUser
     * @param User $toUser
     * @param mixed $amount
     * @return void
     */
    public function walletToWalletTransfer(User $fromUser, User $toUser, int $amount)
    {
        return DB::transaction(function () use ($fromUser, $toUser, $amount) {

            $fromBookId = Book::where('book_src_id', $fromUser->id)
                ->where('book_type', BookEnum::CUSTOMER)
                ->value('book_id');

            $toBookId = Book::where('book_src_id', $toUser->id)
                ->where('book_type', BookEnum::CUSTOMER)
                ->value('book_id');

            if (is_null($toUser) || is_null($fromUser)) {
                abort(400, "Wallet transfer not successful");
            }

            //Then we deduct amount from one wallet and credit another wallet
            $allTX = $this->setLedgerVariables($amount, $fromBookId, $toBookId, 'Money transfer of ' .$amount);

            //post ledger
            return resolve(AccountingService::class)->postLedger($allTX);
        });
    }

    /**
     * Fund user wallet through account funding on Paystack or FLutterwave
     * @param int $amount
     * @param int $deductBookId
     * @param int $creditBookId
     * @param string $description
     * @param string|null $reference
     * @return array
     */
    public function accountFundingLedger(float $amount, string $reference, int $userId, string $book_type): array
    {
        $accountFundingLedgerId = Ledger::where(['ledger_name' => LedgerEnum::ACCOUNT_FUNDING])->value('ledger_id');
        $accountFundingBookId = Book::where([
            'book_src_id' => $accountFundingLedgerId,
            'book_type' => BookEnum::LEDGER
        ])
            ->value('book_id');

        $userBookId = Book::where('book_src_id', $userId)
            ->where('book_type', $book_type)
            ->value('book_id');

        //deducting fom general bonus ledger
        $allTX = $this->setLedgerVariables($amount, $accountFundingBookId, $userBookId, 'Account funding of ' .$amount, $reference);

        //post ledger
        return resolve(AccountingService::class)->postLedger($allTX);
    }

    /**
     *
     * @param int $amount
     * @param int $deductBookId
     * @param int $creditBookId
     * @param string $description
     * @param string|null $reference
     * @return array
     */
    private function setLedgerVariables(int $amount, int $deductBookId, int $creditBookId, string $description, string $reference = null, $reversal = false): array
    {
        $allTX = $params = [];
        $reference = !is_null($reference) ? $reference : 'GB'.strtoupper(Str::random(10));

        if($reversal)
        {
            //refund to wallet
            $params['amount'] = -1*$amount;
            $params['tx'] = $reference;
            $params['book_id'] = $deductBookId;
            $params['value_date'] = $params['payment_date'] = date('Y-m-d');
            $params['memo'] = $description;
            $allTX[] = $params;

            // debit from ledger
            $params['amount'] = $amount;
            $params['tx'] = $reference;
            $params['book_id'] = $creditBookId;
            $params['value_date'] = $params['payment_date'] = date('Y-m-d');
            $params['memo'] = $description;
            $allTX[] = $params;

            return $allTX;
        }

        //deducting from wallet
        $params['amount'] = $amount;
        $params['tx'] = $reference;
        $params['book_id'] = $deductBookId;
        $params['value_date'] = $params['payment_date'] = date('Y-m-d');
        $params['memo'] = $description;
        $allTX[] = $params;

        // Credit from wallet
        $params['amount'] = -1*$amount;
        $params['tx'] = $reference;
        $params['book_id'] = $creditBookId;
        $params['value_date'] = $params['payment_date'] = date('Y-m-d');
        $params['memo'] = $description;
        $allTX[] = $params;

        return $allTX;
    }


}
