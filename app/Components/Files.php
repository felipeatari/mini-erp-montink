<?php

namespace App\Components;

class Files
{
 private $dir;
 private $create = [];
 private $logs = [];
 private $extensions = [];

 public function extensions()
 {
  if (func_num_args() > 0) {
    return $this->extensions = func_get_args();
  }

  return $this->extensions;
 }

 public function dir($dir)
 {
  return $this->dir = trim($dir, '/');
 }

 public function create($file_name)
 {
  if (! isset($_FILES[$file_name]['type'])) {
    return ['erro' => ['Input ' . $file_name . ' é inválido']];
  }

  $errors = [];
  $success = [];

  if (is_string($_FILES[$file_name]['type']) and is_string($_FILES[$file_name]['type'])) {

    if ($_FILES[$file_name]['error']) {
      $errors[] = ['error' => ['file' => $_FILES[$file_name]['name']]];
    }
    else {
      $tmp_name = $_FILES[$file_name]['tmp_name'];

      $extension = pathinfo($_FILES[$file_name]['name'], PATHINFO_EXTENSION);

      $file_name_save = uniqid() . '.' . $extension;

      $dir = $this->dir . '/' . $file_name_save;

      $success[] = [
        'file' => $_FILES[$file_name]['name'],
        'tmp_name' => $tmp_name,
        'dir' => $dir,
        'extension' => $extension
      ];
    }
  }
  elseif (is_array($_FILES[$file_name]['type'])) {
    for ($i = 0; $i < count($_FILES[$file_name]['type']); $i++):
      foreach ($_FILES as $file):

        if ($file['error'][$i]) {
          $errors[] = ['error' => ['file' => $file['name'][$i]]];
        }
        else {
          $tmp_name = $file['tmp_name'][$i];

          $extension = pathinfo($file['name'][$i], PATHINFO_EXTENSION);

          $file_name_save = uniqid() . '.' . $extension;

          $dir = $this->dir . '/' . $file_name_save;

          $success[] = [
            'file' => $file['name'][$i],
            'tmp_name' => $tmp_name,
            'dir' => $dir,
            'extension' => $extension
          ];
        }
      endforeach;
    endfor;
  }

  $this->create = ['success' => $success, 'errors' => $errors];
 }

 public function save()
 {
  $file_error = [];
  $msg_error = [];
  $file_success = [];
  $msg_success = [];
  $upload = [];

  foreach ($this->create['success'] as $success):

    if (! in_array($success['extension'], $this->extensions)) {
      $file_error[] = $success['name'];
      $msg_error[] = 'Extensão "' . $success['extension'] . '" do arquivo "' . str_replace('.' . $success['extension'], '', $success['name']) . '" não permitida';
      continue;
    }

    if (! move_uploaded_file($success['tmp_name'], $success['dir'])) {
      $file_error[] = $success['name'];
      $msg_error[] = 'Falha ao salvar o arquivo';
      continue;
    }

    $upload[] = true;
    $file_success[] = $success['file'];
    $msg_success[] = 'Arquivo "' . $success['file'] . '" salvo';
  endforeach;

  if (! empty($this->create['errors'])) {
    foreach ($this->create['errors'] as $error):
      $file_error[] = $error['error']['file'];
      $msg_error[]= 'Falha no recebimento do arquivo "' . $error['error']['file'] . '"';
    endforeach;
  }

  if (! empty($file_error) and ! empty($msg_error)) {
    $this->logs[] = [
      'status' => 'error',
      'file' => $file_error,
      'msg' => $msg_error,
      'date' => date('Y-m-d H:i:s'),
     ];
  }

  if (empty($upload)) {
    return false;
  }

  $this->logs[] = [
    'status' => 'success',
    'file' => $file_success,
    'msg' => $msg_success,
    'date' => date('Y-m-d H:i:s'),
  ];

  return true;
 }

 public function logs()
 {
  return $this->logs;
 }
}