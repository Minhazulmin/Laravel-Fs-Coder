<?php

use Minhazulmin\Fscoder\Http\Controllers\FscoderController;

# Route define
Route::get( '/fscoder', [FscoderController::class, 'index'] )->name( 'fscoder.index' );
Route::post( '/fscoder/create-folder', [FscoderController::class, 'createFolder'] )->name( 'fscoder.createFolder' );
Route::post( '/fscoder/create-file', [FscoderController::class, 'createFile'] )->name( 'fscoder.createFile' );
Route::post( '/fscoder/edit-file', [FscoderController::class, 'editFile'] )->name( 'fscoder.editFile' );
Route::post( '/load-file-content', [FscoderController::class, 'loadFileContent'] )->name( 'fscoder.loadFileContent' );