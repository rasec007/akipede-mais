<?php
// api/index.php
error_reporting(0);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/auth/jwt.php';
require_once __DIR__ . '/auth/check_session.php';

$request_method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Para lidar com "/api/index.php/produtos" corretamente:
// Pega o nome do recurso após "index.php/" ou "/api/"
$resource = '';
if (strpos($uri, 'index.php/') !== false) {
    $parts = explode('index.php/', $uri);
    $sub_parts = explode('/', $parts[1]);
    $resource = $sub_parts[0];
} else {
    $parts = explode('/api/', $uri);
    if (count($parts) > 1) {
        $sub_parts = explode('/', $parts[1]);
        $resource = $sub_parts[0];
    }
}

// Proteção da API (Exceto health e login se houvesse no index)
if ($resource !== 'health' && $resource !== 'login' && !isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["message" => "Não autorizado"]);
    exit();
}

// Libera trava do arquivo de sessão para permitir conexões concorrentes do mesmo usuário (melhora performance e evita deadlock)
session_write_close();

switch($resource) {
    case 'health':
        echo json_encode(["status" => "ok", "message" => "Akipede Mais API is running"]);
        break;
    case 'produtos':
        require_once __DIR__ . '/controllers/ProdutoController.php';
        $controller = new ProdutoController((new Database())->getConnection());
        if ($request_method == 'GET') {
            $user_id = $_SESSION['user']['id'] ?? null;
            if ($user_id) {
                echo json_encode($controller->readByUser($user_id));
            } else {
                echo json_encode([]); // Falha de segurança, não tem sessão
            }
        } elseif ($request_method == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($_SESSION['user']['loja_id'])) {
                $data['loja'] = $_SESSION['user']['loja_id'];
            }
            $result = $controller->create($data);
            if ($result) {
                echo json_encode(["message" => "Produto criado", "id" => $result]);
            } else {
                http_response_code(500); 
                echo json_encode(["message" => "Erro ao criar produto", "error" => $controller->getLastError()]);
            }
        } elseif ($request_method == 'PUT') {
            $id = $_GET['id'] ?? null;
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($_SESSION['user']['loja_id'])) {
                $data['loja'] = $_SESSION['user']['loja_id'];
            }
            if ($id && $controller->update($id, $data)) {
                echo json_encode(["message" => "Produto atualizado"]);
            } else {
                http_response_code(500); 
                echo json_encode(["message" => "Erro ao atualizar produto", "error" => $controller->getLastError()]);
            }
        } elseif ($request_method == 'DELETE') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $result = $controller->delete($id);
                if ($result === 'deactivated') {
                    echo json_encode(["message" => "Produto vinculado a orçamentos. Foi desativado em vez de excluído.", "type" => "deactivated"]);
                } elseif ($result) {
                    echo json_encode(["message" => "Produto excluído com sucesso", "type" => "deleted"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Erro ao excluir produto", "error" => $controller->getLastError()]);
                }
            }
        }
        break;
    case 'orcamentos':
        require_once __DIR__ . '/controllers/OrcamentoController.php';
        $controller = new OrcamentoController((new Database())->getConnection());
        if ($request_method == 'GET') {
            $loja_id = $_GET['loja_id'] ?? ($_SESSION['user']['loja_id'] ?? null);
            echo json_encode($controller->read($loja_id));
        } elseif ($request_method == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($_SESSION['user']['loja_id'])) {
                $data['loja'] = $_SESSION['user']['loja_id'];
            }
            $id = $controller->create($data);
            if ($id) {
                http_response_code(201); echo json_encode(["message" => "Orçamento criado", "id" => $id]);
            } else {
                http_response_code(500); echo json_encode(["message" => "Erro ao criar orçamento"]);
            }
        }
        break;

    case 'disponibilidade':
        require_once __DIR__ . '/controllers/OrcamentoController.php';
        $controller = new OrcamentoController((new Database())->getConnection());
        if ($request_method == 'GET') {
            $produto_id = $_GET['produto_id'] ?? null;
            $inicio = $_GET['inicio'] ?? null;
            $fim = $_GET['fim'] ?? null;
            if ($produto_id && $inicio && $fim) {
                $resultado = $controller->getDisponibilidade($produto_id, $inicio, $fim);
                echo json_encode(["status" => "success", "qtd_prevista" => $resultado]);
            } else {
                http_response_code(400); echo json_encode(["message" => "Parâmetros faltando"]);
            }
        }
        break;
    case 'pedidos':
        require_once __DIR__ . '/controllers/PedidoController.php';
        $controller = new PedidoController((new Database())->getConnection());
        if ($request_method == 'GET') {
            $loja_id = $_GET['loja_id'] ?? ($_SESSION['user']['loja_id'] ?? null);
            echo json_encode($controller->read($loja_id));
        }
        break;
    case 'clientes':
        require_once __DIR__ . '/controllers/ClienteController.php';
        $controller = new ClienteController((new Database())->getConnection());
        if ($request_method == 'GET') {
            $loja_id = $_GET['loja_id'] ?? ($_SESSION['user']['loja_id'] ?? null);
            echo json_encode($controller->read($loja_id));
        } elseif ($request_method == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($_SESSION['user']['loja_id'])) {
                $data['loja'] = $_SESSION['user']['loja_id'];
            }
            if ($controller->create($data)) {
                echo json_encode(["message" => "Cliente criado"]);
            } else {
                http_response_code(500); 
                echo json_encode(["message" => "Erro ao criar cliente", "error" => $controller->getLastError()]);
            }
        } elseif ($request_method == 'PUT') {
            $id = $_GET['id'] ?? null;
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($_SESSION['user']['loja_id'])) {
                $data['loja'] = $_SESSION['user']['loja_id'];
            }
            if ($id && $controller->update($id, $data)) {
                echo json_encode(["message" => "Cliente atualizado"]);
            } else {
                http_response_code(500); 
                echo json_encode(["message" => "Erro ao atualizar cliente", "error" => $controller->getLastError()]);
            }
        } elseif ($request_method == 'DELETE') {
            $id = $_GET['id'] ?? null;
            if ($id && $controller->delete($id)) {
                echo json_encode(["message" => "Cliente excluído"]);
            } else {
                http_response_code(500); 
                echo json_encode(["message" => "Erro ao excluir cliente", "error" => $controller->getLastError()]);
            }
        }
        break;
    case 'users':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController((new Database())->getConnection());
        if ($request_method == 'GET') {
            echo json_encode($controller->read());
        } elseif ($request_method == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            if ($controller->create($data)) {
                echo json_encode(["message" => "Usuário criado"]);
            } else {
                http_response_code(500); echo json_encode(["message" => "Erro ao criar usuário"]);
            }
        }
        break;
    case 'categorias':
        require_once __DIR__ . '/controllers/CategoriaController.php';
        $controller = new CategoriaController((new Database())->getConnection());
        if ($request_method == 'GET') {
            $loja_id = $_GET['loja_id'] ?? ($_SESSION['user']['loja_id'] ?? null);
            echo json_encode($controller->read($loja_id));
        } elseif ($request_method == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            // Reforçar o loja_id vindo da sessão para segurança
            if (isset($_SESSION['user']['loja_id'])) {
                $data['loja'] = $_SESSION['user']['loja_id'];
            }
            $id = $controller->create($data);
            if ($id) {
                http_response_code(201);
                echo json_encode(["message" => "Categoria criada", "id" => $id]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao criar categoria"]);
            }
        }
        break;
    case 'loja':
        require_once __DIR__ . '/controllers/LojaController.php';
        $controller = new LojaController((new Database())->getConnection());
        $id = $_GET['id'] ?? null;
        if ($id) {
            echo json_encode($controller->getById($id));
        }
        break;
    case 'dashboard-stats':
        require_once __DIR__ . '/controllers/StatsController.php';
        $controller = new StatsController((new Database())->getConnection());
        $loja_id = $_GET['loja_id'] ?? ($_SESSION['user']['loja_id'] ?? null);
        if ($loja_id) {
            echo json_encode($controller->getDashboardStats($loja_id));
        } else {
            http_response_code(400);
            echo json_encode(["message" => "loja_id não fornecido"]);
        }
        break;
    case 'agenda':
        require_once __DIR__ . '/controllers/AgendaController.php';
        $controller = new AgendaController((new Database())->getConnection());
        if ($request_method == 'GET') {
            $produto_id = $_GET['produto_id'] ?? null;
            if ($produto_id) {
                echo json_encode($controller->getByProduto($produto_id));
            } else {
                echo json_encode([]);
            }
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "Recurso não encontrado"]);
        break;
}
?>
