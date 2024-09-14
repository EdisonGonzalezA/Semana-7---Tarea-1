<?php

require('fpdf/fpdf.php');
require_once("../models/productos.model.php");
class PDF_Invoice extends FPDF
{
    // Header
    function Header()
    {
        // Logo
        $this->Image('../public/images/optica.jpg', 10, 6, 50);
        $this->SetFont('Arial', 'B', 21);
        $this->Cell(190, 10, 'Optica Sooleil', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(190, 6, 'RUC: 1234567890', 0, 1, 'C');
        $this->Cell(190, 6, 'Direccion: Calle Falsa 123, Quito, Ecuador', 0, 1, 'C');
        $this->Cell(190, 6, 'Telefono: +593 999 999 999', 0, 1, 'C');
        $this->Cell(190, 6, 'Email: info@empresa.com', 0, 1, 'C');
        $this->Ln(10);
        $this->Cell(190, 6, 'Factura No. 001-001-000000001', 0, 1, 'R');
        $this->Cell(190, 6, 'Fecha de Emision: ' . date('d/m/Y'), 0, 1, 'R');
        $this->Ln(10);
    }

    // Footer
    function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Forma de Pago: Transferencia Bancaria', 0, 1, 'L');
        $this->Cell(0, 10, 'Cuenta Bancaria: Banco Pichincha, Cta: 123456789', 0, 1, 'L');
        $this->Cell(0, 10, 'Nota: Gracias por su compra.', 0, 1, 'L');
    }

    // Cabecera de la tabla
    function ProductTableHeader()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(45, 10, 'Descripcion', 1);
        $this->Cell(20, 10, 'Cantidad', 1);
        $this->Cell(30, 10, 'Precio Unitario', 1);
        $this->Cell(30, 10, 'Subtotal', 1);
        $this->Cell(30, 10, 'IVA (15%)', 1);
        $this->Cell(35, 10, 'Total', 1);
        $this->Ln();
    }

    // Filas de la tabla
    function ProductRow($codigo_barras, $nombre_producto, $cantidad, $valor_venta, $iva)
    {
        $subtotal = $cantidad * $valor_venta;
        $iva_value = $subtotal * ($iva / 100);
        $total = $subtotal + $iva_value;

        $this->SetFont('Arial', '', 10);
        $this->Cell(45, 10, $nombre_producto, 1);
        $this->Cell(20, 10, $cantidad, 1, 0, align: 'C');
        $this->Cell(30, 10, '$' . number_format($valor_venta, 2), 1, 0, align: 'C');
        $this->Cell(30, 10, '$' . number_format($subtotal, 2), 1, 0, align: 'C');
        $this->Cell(30, 10, '$' . number_format($iva_value, 2), 1, 0, align: 'C');
        $this->Cell(35, 10, '$' . number_format($total, 2), 1, 0, align: 'C');
        $this->Ln();
    }

    // Subtotal, IVA y Total
    function InvoiceTotals($subtotal, $iva, $total)
    {
        $this->Ln(10);
        $this->Cell(155, 10, 'Subtotal', 1, 0, 'R');
        $this->Cell(35, 10, '$' . number_format($subtotal, 2), 1, 1, 'R');
        $this->Cell(155, 10, 'IVA (15%)', 1, 0, 'R');
        $this->Cell(35, 10, '$' . number_format($iva, 2), 1, 1, 'R');
        $this->Cell(155, 10, 'Total a Pagar', 1, 0, 'R');
        $this->Cell(35, 10, '$' . number_format($total, 2), 1, 1, 'R');
    }
}

// Crea el PDF
$pdf = new PDF_Invoice();
$pdf->AddPage();

// Información del cliente
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(100, 10, 'Datos del Cliente', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 6, 'Nombre: Juan Perez', 0, 1);
$pdf->Cell(100, 6, 'Cedula/RUC: 1234567890', 0, 1);
$pdf->Cell(100, 6, 'Direccion: Calle Ejemplo 456, Guayaquil, Ecuador', 0, 1);
$pdf->Cell(100, 6, 'Telefono: +593 987 654 321', 0, 1);
$pdf->Ln(10);

// Encabezado de la tabla de productos
$pdf->ProductTableHeader();

// Obtener productos de la base de datos
$productos = new Producto();
$listaproductos = $productos->todos();

$subtotal = 0;
$iva_total = 0;
$total = 0;

// Iterar sobre los productos y añadir filas
while ($prod = mysqli_fetch_assoc($listaproductos)) {
    $codigo_barras = $prod['Codigo_Barras'];
    $nombre_producto = $prod['Nombre_Producto'];
    $cantidad = $prod['Cantidad'];
    $valor_venta = $prod['Valor_Venta'];
    $iva = $prod['Graba_IVA'] == 1 ? 15 : 0; // Suponiendo un 15% de IVA 

    $pdf->ProductRow($codigo_barras, $nombre_producto, $cantidad, $valor_venta, $iva);

    $subtotal += $cantidad * $valor_venta;
    $iva_total += $subtotal * ($iva / 100);
    $total = $subtotal + $iva_total;
}

// Totales
$pdf->InvoiceTotals($subtotal, $iva_total, $total);

// Salida PDF
$pdf->Output();
