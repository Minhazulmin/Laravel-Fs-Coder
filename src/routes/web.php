<?php

use Minhazulmin\Fscoder\Http\Controllers\FscoderController;

Route::get( '/fscoder', [FscoderController::class, 'index'] )->name( 'folders.index' );
Route::post( '/fscoder/create-folder', [FscoderController::class, 'createFolder'] )->name( 'folders.createFolder' );
Route::post( '/fscoder/create-file', [FscoderController::class, 'createFile'] )->name( 'folders.createFile' );
Route::post( '/fscoder/edit-file', [FscoderController::class, 'editFile'] )->name( 'folders.editFile' );
Route::post( '/load-file-content', [FscoderController::class, 'loadFileContent'] )->name( 'folders.loadFileContent' );