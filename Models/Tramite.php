<?php
class Tramite {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function guardar(array $datos) {
        $stmt = $this->pdo->prepare("
            INSERT INTO tramites 
            (ticket, tipo_tramite, nombre_alumno, numero_control, correo_alumno, carrera_seleccionada, datos, fecha_envio, estado, archivo) 
            VALUES (:ticket, :tipo, :nombre, :num, :correo, :carrera, :datos, :fecha, :estado, :archivo)
        ");
        return $stmt->execute([
            ':ticket'  => $datos['ticket'],
            ':tipo'    => $datos['tipo_tramite'],
            ':nombre'  => $datos['nombre_alumno'],
            ':num'     => $datos['numero_control'],
            ':correo'  => $datos['correo_alumno'],
            ':carrera' => $datos['carrera_seleccionada'],
            ':datos'   => $datos['datos_json'],
            ':fecha'   => $datos['fecha_envio'],
            ':estado'  => $datos['estado'],
            ':archivo' => $datos['archivo'] ?? null
        ]);
    }

    public function obtenerPorTicket($ticket) {
        $stmt = $this->pdo->prepare("SELECT * FROM tramites WHERE ticket = :ticket");
        $stmt->execute([':ticket' => $ticket]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function listar($filtros = [], $limit = 25, $offset = 0) {
        $where = [];
        $params = [];
        if (!empty($filtros['tipo'])) {
            $where[] = 'tipo_tramite = :tipo';
            $params[':tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['estado'])) {
            $where[] = 'estado = :estado';
            $params[':estado'] = $filtros['estado'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $where[] = 'fecha_envio >= :desde';
            $params[':desde'] = $filtros['fecha_desde'] . ' 00:00:00';
        }
        if (!empty($filtros['fecha_hasta'])) {
            $where[] = 'fecha_envio <= :hasta';
            $params[':hasta'] = $filtros['fecha_hasta'] . ' 23:59:59';
        }
        if (!empty($filtros['buscar'])) {
            $where[] = '(ticket LIKE :buscar OR nombre_alumno LIKE :buscar OR numero_control LIKE :buscar OR correo_alumno LIKE :buscar)';
            $params[':buscar'] = "%" . $filtros['buscar'] . "%";
        }

        $sql = "SELECT * FROM tramites";
        if ($where) $sql .= " WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY fecha_envio DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) $stmt->bindValue($key, $val);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
