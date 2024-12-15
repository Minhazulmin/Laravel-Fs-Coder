<?php

namespace Minhazulmin\Fscoder\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class FscoderController extends Controller {
    // Method to get all folders and files and return tree structure
    private function getAllFoldersAndFilesTree( $path, $prefix = '', $isLast = false ) {
        $items       = [];
        $directories = File::directories( $path ); // Get all folders
        $files       = File::files( $path ); // Get all files

        $allItems   = array_merge( $directories, $files );
        $totalItems = count( $allItems );

        foreach ( $allItems as $index => $item ) {
            $isDirectory = is_dir( $item );
            $isLastItem  = $index === $totalItems - 1;

            $baseName = basename( $item );

            // Skip specific folders like vendor
            if ( $isDirectory && in_array( $baseName, ['vendor', 'node_modules', 'storage'] ) ) {
                continue;
            }

            $treeLine = $prefix . ( $isLastItem ? '└── ' : '├── ' ) . $baseName;

            $items[] = ['path' => $item, 'tree' => $treeLine];

            if ( $isDirectory ) {
                $subPrefix = $prefix . ( $isLastItem ? '    ' : '│   ' );
                $items     = array_merge( $items, $this->getAllFoldersAndFilesTree( $item, $subPrefix ) );
            }
        }

        return $items;
    }

    // Display the folder structure and files in the UI
    public function index() {

        $rootPath = base_path(); // Root path to start scanning
        $items    = $this->getAllFoldersAndFilesTree( $rootPath ); // Get the directory tree structure
        return view( 'fsCoderView::index', compact( 'items' ) );
    }

    // Create a new folder
    public function createFolder( Request $request ) {

        $validator = Validator::make( $request->all(), [
            'parent_path'     => 'required|string',
            'new_folder_name' => 'required|string|max:255',
        ] );

        if ( $validator->fails() ) {
            return redirect()->back()->withErrors( $validator )->withInput();
        } else {
            $parentPath    = $request->input( 'parent_path' );
            $newFolderPath = $parentPath . '/' . $request->input( 'new_folder_name' );

            // Create the folder if it does not exist
            if ( !File::exists( $newFolderPath ) ) {
                File::makeDirectory( $newFolderPath, 0755, true );
                return redirect()->with( 'success', 'Folder created successfully.' );
            }

            return redirect()->back()->with( 'error', 'Folder already exists.' );
        }
    }

    // Create a new file
    public function createFile( Request $request ) {

        $validator = Validator::make( $request->all(), [
            'parent_path'   => 'required',
            'new_file_name' => 'required|string|max:255',
            'file_content'  => 'required|string',
        ] );

        if ( $validator->fails() ) {
            return redirect()->back()->withErrors( $validator )->withInput();
        } else {
            $parentPath  = $request->input( 'parent_path' );
            $newFilePath = $parentPath . '/' . $request->input( 'new_file_name' );

            // Create the file with the content
            if ( !File::exists( $newFilePath ) ) {
                File::put( $newFilePath, $request->input( 'file_content' ) );
                return redirect()->back()->with( 'success', 'File created successfully.' );
            }

            return redirect()->back()->with( 'error', 'File already exists.' );
        }

    }

    // Edit an existing file
    public function editFile( Request $request ) {

        $validator = Validator::make( $request->all(), [
            'file_path'         => 'required',
            'file_content_edit' => 'required|string',
        ] );

        if ( $validator->fails() ) {
            return redirect()->back()->withErrors( $validator )->withInput();
        } else {

            $filePath = $request->input( 'file_path' );

            // Check if the file exists
            if ( File::exists( $filePath ) ) {
                File::put( $filePath, $request->input( 'file_content_edit' ) );
                return redirect()->back()->with( 'success', 'File updated successfully.' );
            }

            return redirect()->back()->with( 'error', 'File not found.' );
        }

    }

    public function loadFileContent( Request $request ) {
        if ( File::exists( $request->file_path ) ) {
            $content = File::get( $request->file_path );
            return response()->json( ['content' => $content] );
        }

        return response()->json( ['error' => 'File not found'], 404 );
    }
}