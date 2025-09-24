<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocumentStore
{
    protected $basePath;
    
    public function __construct()
    {
        $this->basePath = storage_path('app/documents');
        if (!File::exists($this->basePath)) {
            File::makeDirectory($this->basePath, 0755, true);
        }
    }
    
    /**
     * Store a document in a collection
     */
    public function store($collection, $data)
    {
        $collectionPath = $this->basePath . '/' . $collection;
        if (!File::exists($collectionPath)) {
            File::makeDirectory($collectionPath, 0755, true);
        }
        
        $id = $data['_id'] ?? Str::uuid()->toString();
        $data['_id'] = $id;
        $data['created_at'] = now()->toISOString();
        $data['updated_at'] = now()->toISOString();
        
        $filePath = $collectionPath . '/' . $id . '.json';
        File::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
        
        return $data;
    }
    
    /**
     * Find a document by ID
     */
    public function find($collection, $id)
    {
        $filePath = $this->basePath . '/' . $collection . '/' . $id . '.json';
        
        if (!File::exists($filePath)) {
            return null;
        }
        
        return json_decode(File::get($filePath), true);
    }
    
    /**
     * Find all documents in a collection
     */
    public function findAll($collection, $limit = null)
    {
        $collectionPath = $this->basePath . '/' . $collection;
        
        if (!File::exists($collectionPath)) {
            return [];
        }
        
        $files = File::files($collectionPath);
        $documents = [];
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $data = json_decode(File::get($file->getPathname()), true);
                $documents[] = $data;
            }
            
            if ($limit && count($documents) >= $limit) {
                break;
            }
        }
        
        // Sort by created_at desc
        usort($documents, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $documents;
    }
    
    /**
     * Find documents with simple filtering
     */
    public function findWhere($collection, $filters = [])
    {
        $allDocuments = $this->findAll($collection);
        
        if (empty($filters)) {
            return $allDocuments;
        }
        
        return array_filter($allDocuments, function($document) use ($filters) {
            foreach ($filters as $key => $value) {
                if (!isset($document[$key]) || $document[$key] !== $value) {
                    return false;
                }
            }
            return true;
        });
    }
    
    /**
     * Update a document
     */
    public function update($collection, $id, $data)
    {
        $existing = $this->find($collection, $id);
        if (!$existing) {
            return null;
        }
        
        $data['_id'] = $id;
        $data['created_at'] = $existing['created_at'];
        $data['updated_at'] = now()->toISOString();
        
        $filePath = $this->basePath . '/' . $collection . '/' . $id . '.json';
        File::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
        
        return $data;
    }
    
    /**
     * Delete a document
     */
    public function delete($collection, $id)
    {
        $filePath = $this->basePath . '/' . $collection . '/' . $id . '.json';
        
        if (File::exists($filePath)) {
            File::delete($filePath);
            return true;
        }
        
        return false;
    }
    
    /**
     * Count documents in collection
     */
    public function count($collection)
    {
        return count($this->findAll($collection));
    }
    
    /**
     * Get collection statistics
     */
    public function getStats($collection)
    {
        $documents = $this->findAll($collection);
        $collectionPath = $this->basePath . '/' . $collection;
        
        return [
            'collection' => $collection,
            'count' => count($documents),
            'size_bytes' => File::size($collectionPath) ?: 0,
            'created_at' => File::exists($collectionPath) ? date('Y-m-d H:i:s', File::lastModified($collectionPath)) : null
        ];
    }
}