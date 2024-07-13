<?php

namespace App\Utils;

use Aws\S3\S3Client;
use Intervention\Image\Facades\Image;

class S3Service
{
    public function enviaS3($arquivo, $pasta)
    {
        $key = $pasta.'/'.md5(date('c') . uniqid()) . '.' . $arquivo->getClientOriginalName();

        return $this->S3()->putObject([
            'Bucket' => 'site',
            'Key' => $key,
            'ContentType' => $arquivo->getClientMimeType(),
            'Body' => file_get_contents(realpath($arquivo)),
            'ACL' => 'public-read'
        ]);
    }

    public function enviaXlsS3($nome)
    {
        return $this->S3()->putObject([
            'Bucket' => 'site',
            'Key' => $nome . '.xls',
            'ContentType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Body' => file_get_contents(public_path($nome.'.xls')),
            'ACL' => 'public-read'
        ]);
    }

    public function enviaS3multi($arquivo, $pasta, $index = false)
    {
        if ($index || $index === 0) {

            $key = $pasta. '/' .$arquivo['name'][$index];
        } else {

            $key = $pasta. '/' .$arquivo['name'];
        }
        $key = str_replace('.jpg', '.webp', $key);

        $image = Image::make($arquivo['tmp_name']);

        //redimensiona para 900
        $image->resize(900, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        //reduz a qualidade 60 % e converte para webp
        $content = $image->encode('webp', 60)->getEncoded();

        if ($content === false) {
            throw new \Exception("Não foi possível obter o conteúdo da URL: {$arquivo['tmp_name']}");
        }
        return $this->S3()->putObject([
            'Bucket' => 'site',
            'Key' => $key,
            'ContentType' => 'image/webp',
            'Body' => $content,
            'ACL' => 'public-read',
            'region' => 'sa-east-1',
        ]);
    }
    public function enviaS3multi1($arquivo, $pasta, $index = false)
    {
        if ($index || $index === 0) {

            $key = $pasta. '/' .$arquivo['name'][$index];
        } else {

            $key = $pasta. '/' .$arquivo['name'];
        }
        $image = Image::make($arquivo['tmp_name']);
        $newImage = $image->encode('webp', 90);
        $webpContent = $newImage->getEncoded();

        $content = file_get_contents($index || $index === 0 ? $arquivo['tmp_name'][$index] : $arquivo['tmp_name']);

        if ($content === false) {
            throw new \Exception("Não foi possível obter o conteúdo da URL: {$arquivo['tmp_name']}");
        }
        return $this->S3()->putObject([
            'Bucket' => 'site',
            'Key' => $key,
            'ContentType' => $index || $index === 0 ? $arquivo['type'][$index] :  $arquivo['type'],
            'Body' => $content,
            'ACL' => 'public-read',
            'region' => 'sa-east-1',
        ]);
    }
/*
    public function enviaS3multi($arquivo, $pasta, $index = false)
    {
        if ($index || $index === 0) {

            $key = $pasta.'/'.md5(date('c') . uniqid()) . '.' . $arquivo['name'][$index];
        } else {

            $key = $pasta.'/'.md5(date('c') . uniqid()) . '.' . $arquivo['name'];
        }

        return $this->S3()->putObject([
            'Bucket' => 'site-munaretto',
            'Key' => $key,
            'ContentType' => $index || $index === 0 ? $arquivo['type'][$index] :  $arquivo['type'],
            'Body' => $index || $index === 0 ? file_get_contents(realpath($arquivo['tmp_name'][$index])) : file_get_contents(realpath($arquivo['tmp_name'])),
            'ACL' => 'public-read'
        ]);
    } */

    public function enviaS3Convertida($arquivo, $pasta)
    {

        $novoArquivo = '';
        $newImage = Image::make($arquivo['tmp_name'])->encode('webp', 90);
        $newImage->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $novoArquivo = $newImage->orientate()->stream()->__toString();

        $key = $pasta. '.' . $arquivo['name'];


        return $this->S3()->putObject([
            'Bucket' => 'site',
            'Key' => $key,
            'ContentType' => $arquivo['type'],
            'Body' => $novoArquivo,
            'ACL' => 'public-read'
        ]);
    }
    public function enviaS3miniatura($arquivo, $pasta)
    {
        $novoArquivo = '';
        $newImage = Image::make($arquivo['tmp_name']);
        $newImage->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $novoArquivo = $newImage->orientate()->stream()->__toString();

        $key = $pasta.'/min_'.md5(date('c') . uniqid()) . '.' . $arquivo['name'];

        return $this->S3()->putObject([
            'Bucket' => 'site',
            'Key' => $key,
            'ContentType' => $arquivo['type'],
            'Body' => $novoArquivo,
            'ACL' => 'public-read'
        ]);
    }

    public function deletaS3($url)
    {
        $arquivo = explode('amazonaws.com/', $url);

        return $this->S3()->deleteObject([
            'Bucket' => 'site',
            'Key' => $arquivo[1],
        ]);
    }


    protected function S3()
    {
        return new S3Client([
            'region' => 'sa-east-1',
            'version' => 'latest',
            'credentials' => [
                'key' => '',
                'secret' => '',
            ],
        ]);
    }
}
