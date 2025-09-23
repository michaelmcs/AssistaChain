<?php


namespace App\Services;

use Web3\Web3;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3p\EthereumTx\Transaction;
use kornrunner\Keccak;
use Elliptic\EC;
use Exception;

class EthereumService
{
    protected Web3 $web3;
    protected string $privateKey;
    protected int $chainId;

    protected ?Contract $contract = null;
    protected ?string $contractAddress = null;

    public function __construct(?string $provider = null, ?string $privateKey = null, ?int $chainId = null)
    {
        $provider   = $provider   ?? config('ethereum.http_provider');
        $privateKey = $privateKey ?? config('ethereum.private_key');
        $chainId    = $chainId    ?? (int) config('ethereum.chain_id', 11155111);

        if (!$provider)   throw new Exception('ETHEREUM_HTTP_PROVIDER no definido');
        if (!$privateKey) throw new Exception('ETHEREUM_PRIVATE_KEY no definido');

        $this->web3       = new Web3(new HttpProvider(new HttpRequestManager($provider, 20)));
        $this->privateKey = $privateKey;
        $this->chainId    = $chainId;

        $abiPath = (string) config('ethereum.contract_abi_path');
        $addr    = (string) config('ethereum.contract_address');
        if ($abiPath && file_exists(base_path($abiPath)) && $addr) {
            $abiJson = file_get_contents(base_path($abiPath));
            $this->contract = new Contract($this->web3->provider, $abiJson);
            $this->contract->at($addr);
            $this->contractAddress = $addr;
        }
    }

    public function getAddressFromPrivateKey(): string
    {
        $pk = ltrim($this->privateKey, '0x');
        $ec = new EC('secp256k1');
        $key = $ec->keyFromPrivate($pk, 'hex');
        $publicKey = $key->getPublic(false, 'hex');
        $publicKeyBin = hex2bin(substr($publicKey, 2));
        $hash = Keccak::hash($publicKeyBin, 256);
        return '0x' . substr($hash, 24);
    }

    public function getNonce(string $address): int
    {
        $nonce = null; $err = null;
        $this->web3->eth->getTransactionCount($address, 'pending', function ($e, $count) use (&$nonce, &$err) {
            if ($e !== null) { $err = $e->getMessage(); return; }
            $nonce = (int) $count->toString();
        });
        if ($err) throw new Exception($err);
        return $nonce ?? 0;
    }

    public function getGasPriceWei(): string
    {
        $price = null; $err = null;
        $this->web3->eth->gasPrice(function ($e, $p) use (&$price, &$err) {
            if ($e !== null) { $err = $e->getMessage(); return; }
            $price = (string) $p;
        });
        if ($err) throw new Exception($err);
        return $price ?: '0';
    }

    public function sendRaw(array $txData): string
    {
        $transaction = new Transaction($txData);
        $signed = '0x' . $transaction->sign(ltrim($this->privateKey, '0x'));

        $txHash = null; $err = null;
        $this->web3->eth->sendRawTransaction($signed, function ($e, $tx) use (&$txHash, &$err) {
            if ($e !== null) { $err = $e->getMessage(); return; }
            $txHash = (string) $tx;
        });
        if ($err) throw new Exception($err);
        if (!$txHash) throw new Exception('No se obtuvo txHash');
        return $txHash;
    }

    public function registerAssistanceOnBlockchain(string $hash): string
    {
        if (!$this->contract || !$this->contractAddress) {
            throw new Exception('Contrato no configurado');
        }

        $from      = $this->getAddressFromPrivateKey();
        $nonce     = $this->getNonce($from);
        $gasPrice  = $this->getGasPriceWei();
        $data      = $this->contract->getData('registerAssistance', [$hash]);

        $txData = [
            'nonce'    => '0x' . dechex($nonce),
            'to'       => $this->contractAddress,
            'data'     => $data,
            'value'    => '0x0',
            'gas'      => '0x' . dechex(200000),
            'gasPrice' => '0x' . dechex((int) $gasPrice),
            'chainId'  => $this->chainId,
        ];

        return $this->sendRaw($txData);
    }

    public function getTransactionReceipt(string $txHash): array
    {
        $receipt = null; $err = null;
        $this->web3->eth->getTransactionReceipt($txHash, function ($e, $r) use (&$receipt, &$err) {
            if ($e !== null) { $err = $e->getMessage(); return; }
            $receipt = $r ? json_decode(json_encode($r), true) : null;
        });
        if ($err) throw new Exception($err);
        return $receipt ?? [];
    }
}
