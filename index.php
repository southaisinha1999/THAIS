<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Controle de Saldo</title>
    <style>
        body {
            background-color: #F0E68C;
        }

        .container {
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            margin: 0 auto;
        }

        .btn {
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 3px;
            color: #fff;
        }

        .btn-danger {
            background-color: #8B4513;
            border: none;
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #8B4513;
            border: none;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group.inline {
            display: inline-block;
            margin-right: 10px;
        }

        .form-group.inline label {
            margin-right: 5px;
        }

        .historico {
            margin-top: 50px;
            max-height: 300px;
            overflow-y: auto;
        }

        .historico ul {
            list-style-type: none;
            padding: 0;
        }

        .historico li {
            margin-bottom: 10px;
        }

        .saldo-atual {
            font-size: 24px;
            font-weight: bold;
        }

        .actions {
            text-align: right;
            margin-top: 10px;
        }
        .input-nome {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 8px;
            font-size: 16px;
            width: 200px;
        }

        .input-valor {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 8px;
            font-size: 16px;
            width: 100px;
    </style>
</head>
<body>
    <?php
    session_start();

    // Verifica se a sessão de histórico está vazia e a inicializa
    if (!isset($_SESSION['historico'])) {
        $_SESSION['historico'] = [];
    }

    // Função para adicionar uma entrada ao histórico
    function adicionarEntrada($nome, $valor) {
        $_SESSION['historico'][] = [
            'tipo' => 'entrada',
            'nome' => $nome,
            'valor' => $valor
        ];
    }

    // Função para adicionar uma saída ao histórico
    function adicionarSaida($nome, $valor) {
        $_SESSION['historico'][] = [
            'tipo' => 'saida',
            'nome' => $nome,
            'valor' => $valor
        ];
    }

    // Função para calcular o saldo total
    function calcularSaldo() {
        $saldo = 0;

        foreach ($_SESSION['historico'] as $item) {
            if ($item['tipo'] == 'entrada') {
                $saldo += $item['valor'];
            } elseif ($item['tipo'] == 'saida') {
                $saldo -= $item['valor'];
            }
        }

        return $saldo;
    }

    /// Verifica se houve uma requisição de adicionar uma entrada ou saída
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['tipo']) && isset($_POST['nome']) && isset($_POST['valor'])) {
            $tipo = $_POST['tipo'];
            $nome = $_POST['nome'];
            $valor = floatval($_POST['valor']);

            if ($tipo == 'entrada') {
                adicionarEntrada($nome, $valor);
            } elseif ($tipo == 'saida') {
                adicionarSaida($nome, $valor);
            }
        } elseif (isset($_POST['limpar_historico'])) {
            // Limpar o histórico
            $_SESSION['historico'] = [];
        } elseif (isset($_POST['cancelar'])) {
            // Cancelar a última entrada no histórico
            array_pop($_SESSION['historico']);
        }
    }
    ?>

    <div class="container">
        <h2>CONTROLE DE CAIXA</h2>

        <div class="saldo">
            <h3>Saldo Atual</h3>
            <p class="saldo-atual">R$<?php echo number_format(calcularSaldo(), 2, ',', '.'); ?></p>
        </div>

        <div class="actions">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="submit" class="btn btn-danger" name="limpar_historico" value="Limpar Histórico">
    </form>
</div>

<div class="actions" style="margin-top: 10px;">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="submit" class="btn btn-primary" name="cancelar" value="Cancelar Última Movimentação">
    </form>
</div>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" required>
                    <option value="entrada">Entrada</option>
                    <option value="saida">Saída</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" required>
            </div>
            <div class="form-group">
                <label for="valor">Valor:</label>
                <input type="number" name="valor" id="valor" step="0.01" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="registrar" value="Registrar">
            </div>
        </form>

        <div class="historico">
            <h3>Histórico de Movimentações</h3>
            <?php if (!empty($_SESSION['historico'])) : ?>
                <ul>
                    <?php foreach ($_SESSION['historico'] as $item) : ?>
                        <li><?php echo ucfirst($item['tipo']) . ': ' . $item['nome'] . ' - R$' . number_format($item['valor'], 2, ',', '.'); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>Nenhuma movimentação registrada.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>