<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ComboBookfairModel;

class ComboBookfair extends BaseController
{
    protected $combobookfairmodel;

    public function __construct()
    {
        $this->combobookfairmodel = new ComboBookfairModel();
    }

    // ================= ADD ORDER FORM =================
    public function addSaleOrReturnOrder()
    {
        $data['title']     = '';
        $data['bookshops'] = $this->combobookfairmodel->getBookshops();
        $data['combos']    = $this->combobookfairmodel->getCombos();

        return view('printorders/bookfair/saveSaleOrReturnOrder', $data);
    }

    // ================= TRANSPORT AJAX =================
    public function getBookshopTransport()
    {
        $id = $this->request->getPost('bookshop_id');

        return $this->response->setJSON(
            $this->combobookfairmodel->getBookshopTransport($id)
        );
    }

    // ================= SAVE ORDER =================
    public function saveSaleOrReturnOrder()
    {
        $comboId = $this->request->getPost('combo_id');

        if (!$comboId) {
            return redirect()->back()->with('error', 'Please select combo.');
        }

        $orderId    = time();
        $createDate = date('Y-m-d H:i:s');

        $totalQty = $this->combobookfairmodel->getComboTotalQty($comboId);

        $orderData = [
            'order_id'                  => $orderId,
            'bookshop_id'               => $this->request->getPost('bookshop_id'),
            'combo_id'                  => $comboId,
            'book_fair_name'            => $this->request->getPost('book_fair_name'),
            'create_date'               => $createDate,
            'preferred_transport'       => $this->request->getPost('preferred_transport'),
            'preferred_transport_name'  => $this->request->getPost('preferred_transport_name'),
            'remark'                    => $this->request->getPost('remark'),
            'total_qty'                 => $totalQty,
            'status'                    => 0
        ];

        try {
            $this->combobookfairmodel->createOrder($orderData, $comboId);
            return redirect()->back()->with('success', 'Order Created Successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ================= RETURN VIEW =================
   public function bookfairBookshopreturnView($orderId)
{
    $result = $this->combobookfairmodel->getReturnOrder($orderId);

    $data['title'] = 'Return Bookfair Order';
    $data['order'] = $result['order'];   // ✅
    $data['books'] = $result['books'];   // ✅

    return view('printorders/bookfair/bookfairBookShopReturn', $data);
}

    // ================= SAVE RETURN =================
    public function bookfairBookshopsaveReturn()
    {
        $orderId   = $this->request->getPost('order_id');
        $returnQty = $this->request->getPost('return_qty');
        $discount  = (float) ($this->request->getPost('discount') ?? 0);

        $this->combobookfairmodel->processReturn($orderId, $returnQty, $discount);

        return redirect()->to('combobookfair/bookfairbookshoppendingorders')
            ->with('success', 'Return Completed');
    }

    // ================= PENDING =================
    public function bookfairBookshopPendingOrders()
    {
        $data['title']  = 'Bookfair Bookshop Sale or Return Orders';
        $data['orders'] = $this->combobookfairmodel->getBookfairOrders(0);

        return view('printorders/bookfair/bookFairDashboard', $data);
    }

    // ================= SHIPPED =================
    public function bookfairBookshopShippedOrders()
    {
        $data['title']  = 'Bookfair – Shipped Orders';
        $data['orders'] = $this->combobookfairmodel->getBookfairOrders(1);

        return view('printorders/bookfair/bookfairBookshopShippedOrders', $data);
    }

    // ================= SOLD =================
    public function bookfairBookshopSoldOrders()
    {
        $data['title']  = '';
        $data['orders'] = $this->combobookfairmodel->getBookfairOrders(2);

        return view('printorders/bookfair/bookfairBookshopSoldOrders', $data);
    }

    // ================= ORDER DETAILS =================
    public function bookfairBookshopOrderDetails($orderId)
    {
        $data['title'] = 'Bookfair BookShop Sold Order Details';
        $data['order'] = $this->combobookfairmodel->getBookfairOrderDetails($orderId);

        if (empty($data['order'])) {
            return redirect()->back()->with('error', 'Order not found');
        }

        return view('printorders/bookfair/bookfairBookshopOrderDetails', $data);
    }

    public function bookfairShippedOrderDetails($orderId)
    {
        $data['title'] = 'Bookfair BookShop Shipped Order Details';
        $data['order'] = $this->combobookfairmodel->getBookfairOrderDetails($orderId);

        if (empty($data['order'])) {
            return redirect()->back()->with('error', 'Order not found');
        }

        return view('printorders/bookfair/bookfairShippedOrderDetails', $data);
    }

    // ================= COMBO =================
    public function bookfairComboDetails()
    {
        $data['title']  = '';
        $data['combos'] = $this->combobookfairmodel->getBookfairCombos();

        return view('printorders/bookfair/comboDashboard', $data);
    }

    public function bookfairComboBooks($comboId)
    {
        $data['title'] = '';
        $data['books'] = $this->combobookfairmodel->getBookfairComboBooks($comboId);

        return view('printorders/bookfair/comboBooks', $data);
    }

    public function comboOrderDetails($comboId)
    {
        $data['title']  = '';
        $data['orders'] = $this->combobookfairmodel->getComboOrders($comboId);

        return view('printorders/bookfair/comboOrderDetails', $data);
    }

    public function comboBookOrders($bookId)
    {
        $data['title']  = '';
        $data['orders'] = $this->combobookfairmodel->getComboBookOrders($bookId);

        return view('printorders/bookfair/comboBookOrders', $data);
    }
}
