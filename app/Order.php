<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const PAYMENT_COMPLETED = 1;
    const PAYMENT_PENDING = 0;

    protected $guarded = ['id'];

    protected $fillable = [
        'transaction_id',
        'amount',
        'payment_status',
        'payment_method',
        'user_id',
        'token',
        'model_id',
        'model_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function scopeToken($query, $token)
    {
        return $this->where('token', $token);
    }

    public function meta()
    {
        return $this->hasMany(OrderMeta::class);
    }

    public function model($when = false): Product
    {
        return Product::getModel($this->model_type, $this->model_id, $this, $when);
    }

    public function getCompletedInfo(): String
    {
        return $this->model('order-summary')->getCompletedText($this);
    }

    public function getProductName(): String
    {
        return $this->model('order-summary')->getProductName();
    }

    public function getProductPrice($formatted = true)
    {
        return $this->model('order-summary')->getProductPrice($formatted);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function complete($transaction_id)
    {
        $this->update([
            'transaction_id' => $transaction_id,
            'payment_status' => self::PAYMENT_COMPLETED
        ]);

        $this->model('complete-order')->completed($this);

        return $this;
    }

    public function generateInvoice()
    {
        $pdf = \PDF::loadView('invoices.pvm', [
            'order' => $this,
            'orderedProducts' => $this->products
        ]);

        return $pdf;
    }

    public function getInvoiceName()
    {
        return sprintf('invoice-%s.pdf', $this->id);
    }
}
