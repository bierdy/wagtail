<?php

namespace Wagtail\Variables;

class Image extends Variable implements VariableInterface
{
    public function init()
    {
        $loaded_image = $this->getLoadedImage();
        
        if (is_null($loaded_image))
            return;
    
        $image_name = $loaded_image->getRandomName();
        $options =  json_decode($this->variable->options);
        $image_path = FCPATH . '/' . trim(str_replace('{resource_id}', $this->resource->id, $options->path), '/') .  '/';
        
        if (! is_dir($image_path))
            mkdir($image_path, 0775, true);
    
        if (! empty($this->variable->value->value))
        {
            $old_image_path_info = pathinfo($this->variable->value->value);
            $old_image_name = $old_image_path_info['filename'];
            $old_files = get_filenames($image_path);
            foreach($old_files as $old_file)
                if (strpos($old_file, $old_image_name) !== false)
                    unlink($image_path . $old_file);
            
            $this->variableValueModel->update($this->variable->value->id, ['value' => $image_name]);
        }
        else
            $this->variableValueModel->insert(['resource_id' => $this->resource->id, 'variable_id' => $this->variable->id, 'value' => $image_name, 'order' => 0]);
    
        $loaded_image->move($image_path, $image_name);
        
        if (! empty($options->thumbs))
            Image::createThumbs($options->thumbs, $image_path . $image_name);
    }
    
    protected function getLoadedImage() : ? object
    {
        $request = service('Request');
        $loaded_image = $request->getFile($this->variable->name);
    
        if (is_null($loaded_image) || empty($loaded_image->getClientName()) || empty($loaded_image->getSize()))
            return null;
    
        return $loaded_image;
    }
    
    static function createThumbs(array $thumbs = [], string $image = '')
    {
        $image_path_info = pathinfo($image);
        
        foreach($thumbs as $thumb)
        {
            $thumb_image = $image_path_info['dirname'] . '/' . $image_path_info['filename'] . $thumb->postfix . '.' . $image_path_info['extension'];
            
            if (! file_exists($thumb_image))
                copy($image, $thumb_image);
            
            $imagick = new \Imagick($thumb_image);
            
            foreach($thumb->methods as $method)
            {
                $imagick->{$method->name}(...$method->options);
                
                if ($method->name == 'setImageFormat')
                {
                    $image_format = $method->options[array_key_first($method->options)];
                    
                    if ($image_format == 'webp')
                    {
                        $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);
                        $imagick->setBackgroundColor(new \ImagickPixel('transparent'));
                    }
                    
                    if ($image !== $thumb_image)
                        unlink($thumb_image);
                    
                    $image_path_info['extension'] = $image_format;
                    $thumb_image = $image_path_info['dirname'] . '/' . $image_path_info['filename'] . $thumb->postfix . '.' . $image_path_info['extension'];
                }
            }
            
            $imagick->writeImage($thumb_image);
        }
    }
    
    public function deleteValue(int $value_id = 0)
    {
        $options =  json_decode($this->variable->options);
        $image_path = FCPATH . '/' . trim(str_replace('{resource_id}', $this->resource->id, $options->path), '/') .  '/';
        $image = $this->variableValueModel->find($value_id)->value;
        
        if (file_exists($image_path . $image))
        {
            $image_path_info = pathinfo($image);
            $image_name = $image_path_info['filename'];
            $files = get_filenames($image_path);
            foreach($files as $file)
                if (strpos($file, $image_name) !== false)
                    unlink($image_path . $file);
        }
    
        $this->variableValueModel->delete($value_id);
    
        setWagtailCookie('message', 'The image was successfully deleted.');
    }
}
