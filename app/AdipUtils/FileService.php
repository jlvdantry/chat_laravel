<?php

namespace App\AdipUtils;

use App\Models\Archivo;
use App\AdipUtils\ErrorLoggerService as Logg;
use Carbon\Carbon;
use Str;
use Auth;
use Illuminate\Http\UploadedFile;

final class FileService{

    /**
     * Constantes del modelo
     */
    public const STORAGE_FOLDER_NAME = 'app_archivos';
    

    /**
     * Desactivar instanciación de clase
     */
    private function __construct(){	}

    
    /**
     * Obtiene información de un archivo almacenado
     * 
     * @param String $uuid Identificador de archivo
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return Object
     */
    public static function getFile(String $uuid):Object{
        $archs = Archivo::where('tx_uuid','=',$uuid)->get();
        if(count($archs)===1){
            $arch = $archs[0];
            $realArch = storage_path('app'.DIRECTORY_SEPARATOR.self::STORAGE_FOLDER_NAME.DIRECTORY_SEPARATOR.$arch->tx_uuid.'.dat');
            if ( file_exists($realArch) ){
                return (Object)['real_path' => $realArch, 'archivo' => $arch];
            }else{
                Logg::log(__METHOD__,'No se encontró el archivo '.$uuid, 404);
                abort(404, "No encontrado");
            }
        }else{
            Logg::log(__METHOD__,'El UUID devuelve un valor diferente a 1.', 422);
            abort(422, 'El identificador del archivo no es válido');
        }
    }

    
    /**
     * Obtiene información de un archivo almacenado como público
     * 
     * @param String $uuid Identificador de archivo
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return Object
     */
    public static function getPublicFile(String $uuid):Object{
        $archs = Archivo::where('tx_uuid','=',$uuid)->get();
        if(count($archs)===1){
            $arch = $archs[0];
            if($arch->st_public !== 1){
                Logg::log(__METHOD__,'Se intentó acceder al recurso no público '.$uuid.' usando getPublicFile()', 403);
                abort(403, "El recurso no es de acceso público");
            }
            $realArch = storage_path('app'.DIRECTORY_SEPARATOR.self::STORAGE_FOLDER_NAME.DIRECTORY_SEPARATOR.$arch->tx_uuid.'.dat');
            if ( file_exists($realArch) ){
                return (Object)['real_path' => $realArch, 'archivo' => $arch];
            }else{
                Logg::log(__METHOD__,'No se encontró el archivo '.$uuid, 404);
                abort(404, "No encontrado");
            }
        }else{
            Logg::log(__METHOD__,'El UUID devuelve un valor diferente a 1.', 422);
            abort(422, 'El identificador del archivo no es válido');
        }
    }

    
    /**
     * Almacena un archivo en el Storage del arquetipo
     * 
     * @param Illuminate\Http\UploadedFile $archivo
     * @param bool $isPublic
     * @return Object
     */
    public static function store(UploadedFile $archivo, $isPublic = FALSE):Object{
        $toSave = new Archivo;
        $toSave->nb_archivo = $archivo->getClientOriginalName();
        $toSave->tx_mime_type = $archivo->getMimeType();
        $toSave->nu_tamano = $archivo->getSize();
        $toSave->tx_uuid = Str::uuid()->toString();
        $toSave->tx_sha256 = hash_file('sha256',$archivo->path());
        $toSave->usr_alta = Auth::user()->idUsuario;
        $toSave->st_public = (int)$isPublic;
        $archivo->storeAs(self::STORAGE_FOLDER_NAME, $toSave->tx_uuid.'.dat');
        $toSave->save();
        return (Object)[
            'id' => $toSave->id,
            'nb_archivo' => $toSave->nb_archivo,
            'tx_mime_type' => $toSave->tx_mime_type,
            'nu_tamano' => $toSave->nu_tamano,
            'tx_uuid' => $toSave->tx_uuid,
        ];
    }

    
    /**
     * Copia un archivo del sistema de archivos al storage del arquetipo
     * 
     * @param \SplFileInfo $archivo
     * @param bool $isPublic
     * @return Object
     */
    public static function addToStorage(\SplFileInfo $archivo, $isPublic = FALSE):Object{
        if(!is_file($archivo->getRealPath())){ abort(404, $archivo->getFilename().' no es un archivo'); }
        $toSave = new Archivo;
        $toSave->nb_archivo = $archivo->getFilename();
        $toSave->tx_mime_type = mime_content_type($archivo->getRealPath());
        $toSave->nu_tamano = $archivo->getSize();
        $toSave->tx_uuid = Str::uuid()->toString();
        $toSave->tx_sha256 = hash_file('sha256',$archivo->getRealPath());
        $toSave->usr_alta = 0;
        $toSave->st_public = (int)$isPublic;
        \File::copy(
            $archivo->getRealPath()
            ,storage_path('app'.DIRECTORY_SEPARATOR.self::STORAGE_FOLDER_NAME.DIRECTORY_SEPARATOR.$toSave->tx_uuid.'.dat')
        );
        $toSave->save();
        return (Object)[
            'id' => $toSave->id,
            'nb_archivo' => $toSave->nb_archivo,
            'tx_mime_type' => $toSave->tx_mime_type,
            'nu_tamano' => $toSave->nu_tamano,
            'tx_uuid' => $toSave->tx_uuid,
        ];
    }
	
}