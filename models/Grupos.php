<?php

namespace app\models;

define("_TAMPOB_", 5);

use Yii;

/**
 * This is the model class for table "grupos".
 *
 * @property int $id
 * @property string $codigo
 * @property int $year
 * @property int $cantidadintegrantes
 * @property int $asignaturas_id
 * @property int $metodos_formacion_id
 *
 * @property Chats[] $chats
 * @property Asignaturas $asignaturas
 * @property MetodosFormacion $metodosFormacion
 * @property GruposAlumnos[] $gruposAlumnos
 */
class Grupos extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'grupos';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['asignaturas_id', 'metodos_formacion_id', 'codigo', 'cantidadintegrantes'], 'required'],
            [['asignaturas_id', 'metodos_formacion_id', 'cantidadintegrantes'], 'integer'],
            [['year'], 'string', 'max' => 4],
            [['asignaturas_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asignaturas::className(), 'targetAttribute' => ['asignaturas_id' => 'id']],
            [['metodos_formacion_id'], 'exist', 'skipOnError' => true, 'targetClass' => MetodosFormacion::className(), 'targetAttribute' => ['metodos_formacion_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'year' => 'Año',
            'codigo' => 'Código',
            'cantidadintegrantes' => 'Cantidad de Integrantes',
            'asignaturas_id' => 'Asignaturas ID',
            'metodos_formacion_id' => 'Método de Formación',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChats() {
        return $this->hasMany(Chats::className(), ['grupos_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsignaturas() {
        return $this->hasOne(Asignaturas::className(), ['id' => 'asignaturas_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetodosFormacion() {
        return $this->hasOne(MetodosFormacion::className(), ['id' => 'metodos_formacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposAlumnos() {
        return $this->hasMany(GruposAlumnos::className(), ['grupos_id' => 'id']);
    }

    private static function cmp($a, $b) {
        if ($a["fitness"] == $b["fitness"]) {
            return 0;
        }
        return ($a["fitness"] < $b["fitness"]) ? 1 : -1;
    }

    function random() {
        return mt_rand() / mt_getrandmax();
    }

    function contabilizarProporciones($grupo, $alumnos) {
        $cantidadMiembros = count($grupo);
        $item = array(
            "intuitivo" => 0,
            "sensitivo" => 0,
            "neutral-is" => 0,
            "reflexivo" => 0,
            "activo" => 0,
            "neutral-ar" => 0,
            "global" => 0,
            "secuencial" => 0,
            "neutral-sg" => 0,
            "visual" => 0,
            "verbal" => 0,
            "neutral-vv" => 0
        );

        foreach ($grupo as $miembro) {
            // Se extrae los parentesis que abran y cierran
            list($ar, $is, $vv, $sg) = explode(",", $alumnos[$miembro]["ea"]);
            $item[$ar] += 1;
            $item[$is] += 1;
            $item[$vv] += 1;
            $item[$sg] += 1;
        }

        // Calculo las proporciones
        $item["intuitivo"] /= $cantidadMiembros;
        $item["sensitivo"] /= $cantidadMiembros;
        $item["neutral-is"] /= $cantidadMiembros;
        $item["reflexivo"] /= $cantidadMiembros;
        $item["activo"] /= $cantidadMiembros;
        $item["neutral-ar"] /= $cantidadMiembros;
        $item["global"] /= $cantidadMiembros;
        $item["secuencial"] /= $cantidadMiembros;
        $item["neutral-sg"] /= $cantidadMiembros;
        $item["visual"] /= $cantidadMiembros;
        $item["verbal"] /= $cantidadMiembros;
        $item["neutral-vv"] /= $cantidadMiembros;

        $item["intuitivo"] = $this->DevolverClase($item["intuitivo"]);
        $item["sensitivo"] = $this->DevolverClase($item["sensitivo"]);
        $item["neutral-is"] = $this->DevolverClase($item["neutral-is"]);
        $item["reflexivo"] = $this->DevolverClase($item["reflexivo"]);
        $item["activo"] = $this->DevolverClase($item["activo"]);
        $item["neutral-ar"] = $this->DevolverClase($item["neutral-ar"]);
        $item["global"] = $this->DevolverClase($item["global"]);
        $item["secuencial"] = $this->DevolverClase($item["secuencial"]);
        $item["neutral-sg"] = $this->DevolverClase($item["neutral-sg"]);
        $item["visual"] = $this->DevolverClase($item["visual"]);
        $item["verbal"] = $this->DevolverClase($item["verbal"]);
        $item["neutral-vv"] = $this->DevolverClase($item["neutral-vv"]);

        return $item;
    }

    function DevolverClase($porcentaje) {
        if (($porcentaje >= 0) && ($porcentaje <= 0.445)) {
            return "pocos";
        } else if (($porcentaje > 0.455) && ($porcentaje <= 0.66)) {
            return "regular";
        } else {
            return "muchos";
        }
    }

    function predecirRendimiento($grupo, $alumnos) {
        $estilos = $this->contabilizarProporciones($grupo, $alumnos);
        $rendimiento = ["bueno", "malo"];
        return $rendimiento[rand(0, 1)];
    }

    function determinarFitness($propuesta, $alumnos) {
        $cantidadPositivos = 0;
        foreach ($propuesta as $grupo) {
            $rendimiento = $this->predecirRendimiento($grupo, $alumnos);
            if ($rendimiento == "bueno") {
                $cantidadPositivos += 1;
            }
        }

        return $cantidadPositivos / count($propuesta);
    }

    function formarGruposAzar($alumnos, $cantidadIntegrantes, $cantidadAlternativas) {
        $poblacion = [];
        for ($k = 1; $k <= $cantidadAlternativas; $k++) {
            $alumnosSeleccionados = [];
            $grupos = [];
            $totalAlumnos = count($alumnos);
            for ($i = 0; $i <= ceil($totalAlumnos / $cantidadIntegrantes) - 1; $i++) {
                for ($j = 1; ($j <= $cantidadIntegrantes) && (count($alumnosSeleccionados) != count($alumnos)); $j++) {
                    do {
                        $miembro = rand(0, $totalAlumnos - 1);
                    } while (in_array($miembro, $alumnosSeleccionados));
                    $alumnosSeleccionados[] = $miembro;
                    $grupos[$i][] = $miembro;
                }
            }

            $poblacion[] = ["grupos" => $grupos, "fitness" => $this->determinarFitness($grupos, $alumnos)];
        }

        return $poblacion;
    }

    function imprimirGrupo($grupos, $alumnos) {
        foreach ($grupos["grupos"] as $grupo) {
            foreach ($grupo as $miembro) {
                echo $alumnos[$miembro]["nombre"] . "<br/>";
            }
            echo "<br/>";
        }

        echo "Fitness: " . $grupos["fitness"];
    }

    function elegirPadreTorneo($poblacion, $k) {
        $seleccionados = [];
        $elegido = NULL;
        $mayor = -PHP_INT_MAX;
        for ($i = 1; $i <= $k; $i++) {
            do {
                $candidato = rand(0, _TAMPOB_ - 1);
            } while (in_array($candidato, $seleccionados));

            $seleccionados[] = $candidato;
            if ($poblacion[$candidato]["fitness"] > $mayor) {
                $mayor = $poblacion[$candidato]["fitness"];
                $elegido = $poblacion[$candidato];
            }
        }

        return $elegido;
    }

    function obtenerParametroC($maxRango) {
        $acumulado = 0;
        for ($i = 1; $i <= $maxRango; $i++) {
            $acumulado += (1 - exp(-$i));
        }
        return $acumulado;
    }

    function acumuladoExponencial($i, $c) {
        $acumulado = 0;
        for ($j = 0; $j <= $i; $j++) {
            $acumulado += ((1 - exp(-$j)) / $c);
        }
        return $acumulado;
    }

    function elegirPadrePorRanking($poblacion, $c) {

        $b = false;
        $i = 1;
        $n = $this->random();
        while (!$b) {
            if (($n > $this->acumuladoExponencial($i - 1, $c)) && ($n <= $this->acumuladoExponencial($i, $c))) {
                $b = true;
            } else {
                $i += 1;
            }
        }

        return $poblacion[$i - 1];
    }

    function generarMatingPool($poblacion) {
        // Ordenar la población en caso de elegir padres por rating
        usort($poblacion, array($this, "cmp"));
        // Obtener parámetro c
        $c = $this->obtenerParametroC(count($poblacion));

        $matingPool = [];
        for ($i = 1; $i <= _TAMPOB_; $i++) {
            //$matingPool[] = elegirPadreTorneo($poblacion, 5);

            $matingPool[] = $this->elegirPadrePorRanking($poblacion, $c);
        }
        return $matingPool;
    }

    function realizarCruzamiento($padre1, $padre2) {
        if (random() < 0.5) {
            $puntoCruce = rand(0, min(count($padre1), count($padre2)) - 1);
            $hijo1 = [];
            $hijo2 = [];

            for ($i = 0; $i <= $puntoCruce; $i++) {
                $hijo1[$i] = $padre1[$i];
                $hijo2[$i] = $padre2[$i];
            }

            for ($i = $puntoCruce + 1; $i < count($padre2); $i++) {
                $hijo1[$i] = $padre2[$i];
            }

            for ($i = $puntoCruce + 1; $i < count($padre1); $i++) {
                $hijo2[$i] = $padre1[$i];
            }
        } else {
            $hijo1 = $padre1;
            $hijo2 = $padre2;
        }

        return [$hijo1, $hijo2];
    }

    function realizarCruzamientoUniforme($padre1, $padre2) {
        if ($this->random() < 0.5) {
            $i = 0;
            $hijo1 = [];
            $hijo2 = [];
            while (($i < count($padre1)) && ($i < count($padre2))) {
                if ($this->random() < 0.5) {
                    $hijo1[] = $padre1[$i];
                    $hijo2[] = $padre2[$i];
                } else {
                    $hijo1[] = $padre2[$i];
                    $hijo2[] = $padre1[$i];
                }
                $i = $i + 1;
            }

            for ($j = $i; $j < count($padre1); $j++) {
                $hijo1[] = $padre1[$i];
            }

            for ($j = $i; $j < count($padre2); $j++) {
                $hijo2[] = $padre2[$i];
            }
        } else {
            $hijo1 = $padre1;
            $hijo2 = $padre2;
        }

        return [$hijo1, $hijo2];
    }

    function realizarMutacion($hijo) {
        if ($this->random() < 0.03) {
            //Elije al azar dos grupos
            $grupo1 = rand(0, count($hijo) - 1);
            $grupo2 = rand(0, count($hijo) - 1);

            //Elije los miembros a intercambiar
            $miembro1 = rand(0, count($hijo[$grupo1]) - 1);
            $miembro2 = rand(0, count($hijo[$grupo2]) - 1);

            $aux = $hijo[$grupo1][$miembro1];
            $hijo[$grupo1][$miembro1] = $hijo[$grupo2][$miembro2];
            $hijo[$grupo2][$miembro2] = $aux;
        }

        return $hijo;
    }

    function realizarMutacionMezcla($hijo, $cantidadIntegrantes) {
        if ($this->random() < 0.03) {
            // Selecciona los grupos al azar
            $gruposSeleccionados = [];
            while (count($gruposSeleccionados) < count($hijo) - 2) {
                do {
                    $seleccionado = rand(0, count($hijo) - 1);
                } while (in_array($seleccionado, $gruposSeleccionados));
                $gruposSeleccionados[] = $seleccionado;
            }

            // Selecciona los miembros al azar
            $miembrosSeleccionados = [];
            foreach ($gruposSeleccionados as $nroGrupo) {
                $miembros = [];
                while (count($miembros) < count($hijo[$nroGrupo]) - 2) {
                    do {
                        $seleccionado = rand(0, count($hijo[$nroGrupo]) - 1);
                    } while (in_array($seleccionado, $miembros));
                    $miembros[] = $seleccionado;
                }
                $miembrosSeleccionados[$nroGrupo] = $miembros;
            }

            // Copia los integrantes no seleccionados de los grupos elegidos
            $grupos = [];
            foreach ($gruposSeleccionados as $nroGrupo) {
                foreach ($hijo[$nroGrupo] as $posicion => $integrante) {
                    if (!in_array($posicion, $miembrosSeleccionados[$nroGrupo])) {
                        $grupos[$nroGrupo][] = $integrante;
                    }
                }
            }

            // Mezcla los miembros seleccionados
            foreach ($miembrosSeleccionados as $nroGrupo => $miembros) {
                foreach ($miembros as $posMiembro) {
                    do {
                        $grupoRecibeIntegrante = rand(0, count($gruposSeleccionados) - 1);
                    } while (!(count($grupos[$gruposSeleccionados[$grupoRecibeIntegrante]]) < $cantidadIntegrantes)); // Hace mientras el grupo no tenga el total de integrantes requeridos
                    $grupos[$gruposSeleccionados[$grupoRecibeIntegrante]][] = $hijo[$nroGrupo][$posMiembro];
                }
            }

            // Reemplaza los genes alterados
            foreach ($grupos as $nroGrupo => $integrantes) {
                $hijo[$nroGrupo] = $integrantes;
            }
        }

        return $hijo;
    }

    function reemplazoGeneracional(&$poblacion, $offspiring) {
        $poblacion = null;
        usort($offspiring, "cmp");
        for ($i = 0; $i < _TAMPOB_; $i++) {
            $poblacion[$i] = $offspiring[$i];
        }
    }

    function reemplazoElitismo(&$poblacion, $offspiring) {
        usort($poblacion, array($this, "cmp"));
        usort($offspiring, array($this, "cmp"));
        for ($i = 1; $i < _TAMPOB_; $i++) {
            $poblacion[$i] = $offspiring[$i - 1];
        }
    }

    public function optimizarAG($alumnos, $cantidadMiembros) {
        $poblacion = $this->formarGruposAzar($alumnos, $cantidadMiembros, _TAMPOB_);
        usort($poblacion, array($this, "cmp"));
        $fitnessPrevio = 0;

        $iteraciones = 1;
        while ($iteraciones <= 70) {
            $fitnessPrevio = $poblacion[0]["fitness"];

            // Despues probar sin reposicion a la elección de padres
            $offspiring = [];
            $matingPool = $this->generarMatingPool($poblacion);
            for ($i = 0; $i < count($matingPool) - 1; $i++) {
                $hijos = $this->realizarCruzamientoUniforme($matingPool[$i]["grupos"], $matingPool[$i + 1]["grupos"]);
                $hijos[0] = $this->realizarMutacionMezcla($hijos[0], $cantidadMiembros);
                $hijos[1] = $this->realizarMutacionMezcla($hijos[1], $cantidadMiembros);
                $offspiring[] = $hijos[0];
                $offspiring[] = $hijos[1];
            }


            foreach ($offspiring as $grupos) {
                $aux[] = ["grupos" => $grupos, "fitness" => $this->determinarFitness($grupos, $alumnos)];
            }

            // Reemplazo generacional
            //reemplazoGeneracional($poblacion, $aux);
            $this->reemplazoElitismo($poblacion, $aux);

            $iteraciones += 1;
        }
        //echo "<br/>Total iteraciones: " . ($iteraciones - 1) . "<br/><br/>";
        return $poblacion;
    }

    public static function getListaGrupos() {
        return yii\helpers\ArrayHelper::map(Grupos::find()->all(), 'id', 'codigo');
    }
}
