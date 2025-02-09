<?php
session_start();
require_once 'config/database.php';
require_once 'models/Lista.php';
require_once 'vendor/autoload.php';

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o ID da lista foi fornecido
if (!isset($_GET['lista_id']) || empty($_GET['lista_id'])) {
    header('Location: dashboard.php');
    exit();
}

try {
    // Inicializar lista
    $lista = new Lista();
    $lista->id = $_GET['lista_id'];
    $lista->usuario_id = $_SESSION['user_id'];

    // Buscar detalhes da lista
    $detalhesLista = $lista->obterDetalhesLista();
    if (!$detalhesLista || $detalhesLista['usuario_id'] != $_SESSION['user_id']) {
        throw new Exception("Lista não encontrada ou sem permissão");
    }

    // Buscar itens da lista
    $itens = $lista->buscarItens();

    // Criar PDF
    class MYPDF extends TCPDF {
        public function Header() {}
        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', '', 8);
            $this->SetTextColor(255, 255, 255);
            $this->SetFillColor(59, 130, 246);
            $this->Cell(0, 10, 'Gerado em: ' . date('d/m/Y H:i:s'), 0, false, 'R', true);
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Configurar informações do documento
    $pdf->SetCreator('MyList');
    $pdf->SetAuthor('MyList');
    $pdf->SetTitle($detalhesLista['nome']);

    // Margens personalizadas para um layout mais moderno
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 25);

    // Adicionar página
    $pdf->AddPage();

    // Título do Sistema
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor(59, 130, 246);
    $pdf->Cell(0, 10, 'MyList', 0, 1, 'L');

    // Nome da Lista
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, $detalhesLista['nome'], 0, 1, 'L');

    $pdf->Ln(10);

    // Cabeçalho da Tabela
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetFillColor(59, 130, 246);
    $pdf->SetTextColor(255, 255, 255);

    $pdf->Cell(100, 7, 'Item', 0, 0, 'L', true);
    $pdf->Cell(30, 7, 'Quantidade', 0, 0, 'C', true);
    $pdf->Cell(30, 7, 'Status', 0, 1, 'C', true);

    // Conteúdo da Tabela
    $pdf->SetTextColor(59, 130, 246);
    $pdf->SetFont('helvetica', '', 10);

    foreach ($itens as $item) {
        $status = $item['comprado'] ? 'Comprado' : 'Pendente';
        
        $pdf->Cell(100, 7, $item['nome'], 0, 0, 'L');
        $pdf->Cell(30, 7, $item['quantidade'], 0, 0, 'C');
        $pdf->Cell(30, 7, $status, 0, 1, 'C');
    }

    // Gerar PDF
    $pdf->Output($detalhesLista['nome'] . '.pdf', 'D');
    exit();
} catch (Exception $e) {
    error_log("Erro ao gerar PDF: " . $e->getMessage());
    header('Location: dashboard.php');
    exit();
}
