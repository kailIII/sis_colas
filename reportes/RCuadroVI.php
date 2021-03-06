<?php
//incluimos la libreria
//echo dirname(__FILE__);
//include_once(dirname(__FILE__).'/../PHPExcel/Classes/PHPExcel.php');
class RCuadroVI
{
    private $docexcel;
    private $objWriter;
    private $nombre_archivo;
    private $hoja;
    private $columnas=array();
    private $fila;
    private $equivalencias=array();

    private $indice, $m_fila, $titulo;
    private $objParam;
    public  $url_archivo;
    public $styleTitulos0;
    public $styleTitulos1;
    public $styleTitulos2;
    public $styleDetalle;
    public $styleTotal;


    function __construct(CTParametro $objParam){
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        //ini_set('memory_limit','512M');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");
        $this->docexcel->setActiveSheetIndex(0);
        $this->docexcel->getActiveSheet()->setTitle('Resumen');
        $this->equivalencias=array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
            9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
            18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
            26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
            34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
            42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
            50=>'AY',51=>'AZ',
            52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
            60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
            68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
            76=>'BY',77=>'BZ');
        $this->styleTitulos0 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Calibri'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $this->styleTitulos1 = 	$this->styleTitulos0;
        $this->styleTitulos2 = 	$this->styleTitulos0;
        $this->styleDetalle = 	$this->styleTitulos0;

        $this->styleTitulos1['font']['size'] = 20;
        $this->styleTitulos1['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
        $this->styleTitulos2['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;


        $this->styleTitulos2['fill'] = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'c6d9f1'
            )
        );
        $this->styleTitulos2['borders'] = array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
            )
        );

        $this->styleDetalle['borders'] = array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
            )
        );

        $this->styleDetalle['font']['bold'] = false;

        $this->styleDetalle['font']['size'] = 11;
        $this->styleTotal = 	$this->styleDetalle;
        $this->styleTotal['font']['bold'] = true;
    }

    function imprimeDatos(){
        $parametros = $this->objParam->getParametro('parametros');

        $datos = $this->objParam->getParametro('datos');
        
        

        $this->docexcel->getActiveSheet()->setCellValue('A1','Sucursal : ' . $this->objParam->getParametro('sucursal'));
        $this->docexcel->getActiveSheet()->setCellValue('A2','De : ' . $this->objParam->getParametro('fecha_ini') . " A " . $this->objParam->getParametro('fecha_fin'));
        $usuarios = array();
        $servicios = array();
        foreach ($parametros as $key => $value) {
            if ($value['tipo'] == 'servicio') {
                array_push($servicios,$value['nombre']);
            } elseif ($value['tipo'] == 'usuario') {
                array_push($usuarios,$value['nombre']);
            }
        }
        $this->docexcel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($this->styleTitulos0);

        $this->docexcel->getActiveSheet()->setCellValue('A3','Intervalo');
        $fila = 4;
        foreach ($usuarios as $value) {
            $this->docexcel->getActiveSheet()->setCellValue("A$fila",$value);
            $fila++;
        }
        $this->docexcel->getActiveSheet()->getStyle("A3:A$fila")->applyFromArray($this->styleTitulos0);
        $columna = 1;
        foreach ($servicios as $value) {
            $this->docexcel->getActiveSheet()->setCellValue($this->equivalencias[$columna] .'3',$value);
            $columna++;
        }
        $this->docexcel->getActiveSheet()->getStyle('B3:' . $this->equivalencias[$columna] . '3')->applyFromArray($this->styleTitulos0);
		$puntero = 0;
		 
        for ($i = 0 ; $i < count($usuarios) ; $i++) {
        	for ($j = 0 ; $j < count($servicios) ; $j++) {
        		if ($datos[$puntero]['usuario'] == $usuarios[$i] && $datos[$puntero]['servicio'] == $servicios[$j]) {
        			$this->docexcel->getActiveSheet()->setCellValue($this->equivalencias[$j+1] .($i + 4),$datos[$puntero]['cantidad']);
        			$puntero++;
				} else {	
        			$this->docexcel->getActiveSheet()->setCellValue($this->equivalencias[$j+1] . ($i + 4),'0');	
        		}
        	}
        }

    }



    function generarReporte(){
        //echo $this->nombre_archivo; exit;
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);

    }


}

?>