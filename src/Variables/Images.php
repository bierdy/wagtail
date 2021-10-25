<?php

namespace Wagtail\Variables;

class Images extends Variable implements VariableInterface
{
    public function init()
    {
        $loaded_images = $this->getLoadedImages();
    
        if (! is_null($loaded_images))
            $this->saveLoadedImages($loaded_images);
        
        if (! empty($this->post[$this->variable->name . '_orders']))
            $this->saveImagesOrders();
    }
    
    protected function getLoadedImages() : ? array
    {
        $request = service('Request');
        $loaded_images = $request->getFileMultiple($this->variable->name);
        
        if (is_null($loaded_images))
            return null;
        
        foreach($loaded_images as $key => $loaded_image)
            if (empty($loaded_image->getClientName()) || empty($loaded_image->getSize()))
                unset($loaded_images[$key]);
            
        if (empty($loaded_images))
            return null;
        
        return $loaded_images;
    }
    
    protected function saveLoadedImages(array $loaded_images = [])
    {
        $options =  json_decode($this->variable->options);
        $image_path = FCPATH . '/' . trim(str_replace('{resource_id}', $this->resource->id, $options->path), '/') .  '/';
    
        if (! is_dir($image_path))
            mkdir($image_path, 0775, true);
    
        foreach($loaded_images as $loaded_image)
        {
            $image_name = $loaded_image->getRandomName();
        
            $this->variableValueModel->insert(['resource_id' => $this->resource->id, 'variable_id' => $this->variable->id, 'value' => $image_name, 'order' => 1000]);
        
            $loaded_image->move($image_path, $image_name);
        
            if (! empty($options->thumbs))
                Image::createThumbs($options->thumbs, $image_path . $image_name);
        }
    }
    
    protected function saveImagesOrders()
    {
        $images = $this->post[$this->variable->name . '_orders'];
        
        foreach($images as $image_id => $image_value)
            $this->variableValueModel->update($image_id, ['order' => $image_value]);
    }
    
    public function deleteValue(int $value_id = 0)
    {
        $image_handler = new Image([], $this->resource, $this->variable);
    
        if (method_exists($image_handler, 'deleteValue'))
            $image_handler->deleteValue($value_id);
    }
}