<?php


namespace App\Adapters;


use Illuminate\Support\Facades\DB;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;

class DatabaseStorageAdapter implements AdapterInterface
{
    protected $table = "";
    /**
     * @inheritDoc
     */

    public function __construct()
    {
        $this->table = env('STORAGE_TABLE_NAME');
    }

    public function write($path, $contents, Config $config)
    {
        $path_info = pathinfo($path);
        DB::table($this->table)->insert([
            'DS_EXTENSSAO_ORIGINAL' => $path_info['extension'],
            'DS_MIME_TYPE' => mime_content_type($path_info['extension']),
            'DS_NOME_ORIGINAL' => $path_info['basename'],
            'NU_TAMANHO' => mb_strlen($contents, '8bit'),
            'DS_CAMINHO' => $path,
            'TT_CONTEUDO' => $contents,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function writeStream($path, $resource, Config $config)
    {
        $this->write($path, stream_get_contents($resource), $config);
    }

    /**
     * @inheritDoc
     */
    public function update($path, $contents, Config $config)
    {
        $path_info = pathinfo($path);
        DB::table($this->table)->where(['DS_CAMINHO' => $path])->update([
            'DS_EXTENSSAO_ORIGINAL' => $path_info['extension'],
            'DS_MIME_TYPE' => mime_content_type($path_info['extension']),
            'DS_NOME_ORIGINAL' => $path_info['basename'],
            'NU_TAMANHO' => mb_strlen($contents, '8bit'),
            'DS_CAMINHO' => $path,
            'TT_CONTEUDO' => $contents,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function updateStream($path, $resource, Config $config)
    {
        $this->update($path, stream_get_contents($resource), $config);
        // TODO: Implement updateStream() method.
    }

    /**
     * @inheritDoc
     */
    public function rename($path, $newpath)
    {
        DB::table($this->table)->where(['DS_CAMINHO' => $path])->update(['DS_CAMINHO' => $newpath]);
    }

    /**
     * @inheritDoc
     */
    public function copy($path, $newpath)
    {
        $file = $this->read($path);
        $this->write($newpath, $file['contents'], new Config());
    }

    /**
     * @inheritDoc
     */
    public function delete($path)
    {
        $file = DB::table($this->table)->where(['DS_CAMINHO' =>$path])->first();
        DB::table($this->table)->delete($file->cd_storage);
    }

    /**
     * @inheritDoc
     */
    public function deleteDir($dirname)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function createDir($dirname, Config $config)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * @inheritDoc
     */
    public function has($path)
    {
        return DB::table($this->table)->where(['DS_CAMINHO' => $path])->exists();
    }

    /**
     * @inheritDoc
     */
    public function read($path)
    {
        $file = DB::table($this->table)->where(['DS_CAMINHO' => $path])->first();

        return [
            'extension' => $file['DS_EXTENSSAO_ORIGINAL'],
            'basename' => basename($path),
            'filename' => $file['DS_NOME_ORIGINAL'],
            'dirname' => pathinfo($path)['dirname'],
            'content' => $file['TT_CONTEUDO']
        ];
    }

    /**
     * @inheritDoc
     */
    public function readStream($path)
    {
        // TODO
    }

    /**
     * @inheritDoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        $caminhos = DB::table($this->table)->where('DS_CAMINHO', 'LIKE', "$directory/%")->get(['DS_CAMINHO']);
        return $caminhos->map(function($caminho) { return $this->read($caminho['DS_CAMINHO']); });
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($path)
    {
        // TODO: Implement getMetadata() method.
    }

    /**
     * @inheritDoc
     */
    public function getSize($path)
    {
        $file = DB::table($this->table)->where(['DS_PATH' => $path])->first(['NU_TAMANHO']);
        return $file['NU_TAMANHO'];
    }

    /**
     * @inheritDoc
     */
    public function getMimetype($path)
    {
        $file = DB::table($this->table)->where(['DS_PATH' => $path])->first(['DS_MIME_TYPE']);
        return $file['DS_MIME_TYPE'];
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp($path)
    {
        $file = DB::table($this->table)->where(['DS_PATH' => $path])->first(['ATUALIZADO_EM']);
        return $file['ATUALIZADO_EM'];
    }

    /**
     * @inheritDoc
     */
    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
    }
}
