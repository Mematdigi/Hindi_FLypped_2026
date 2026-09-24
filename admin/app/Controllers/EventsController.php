<?php

namespace App\Controllers;

use App\Models\EventModel;

class EventsController extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        $data['events'] = $eventModel->findAll(); // Fetch all events

        return view('events/index', $data);
    }

    public function create()
    {
        return view('events/create');
    }

    public function store()
    {
        $eventModel = new EventModel();
    
        // Validate and upload the image
        $image = $this->request->getFile('image');
        $imageName = null;
    
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // --- Keep original name and make it SEO friendly ---
            $originalName = $image->getName();
            $ext = $image->getExtension();
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            
            $cleanName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nameWithoutExt), '-'));
            
            if (empty($cleanName)) {
                $cleanName = 'event-' . time();
            }
            
            $imageName = $cleanName . '.' . $ext;
            
            $counter = 1;
            while (file_exists($uploadPath . $imageName)) {
                $imageName = $cleanName . '-' . $counter . '.' . $ext;
                $counter++;
            }
            // --------------------------------------------------

            $image->move($uploadPath, $imageName);
        }
    
        // Save event data to the database
        $eventModel->save([
            'title'         => $this->request->getPost('title'),
            'sub_title'     => $this->request->getPost('sub_title'),
            'description'   => $this->request->getPost('description'),
            'date'          => $this->request->getPost('date'),
            'time'          => $this->request->getPost('time'),
            'location'      => $this->request->getPost('location'),
            'price'         => $this->request->getPost('price'),
            'total_tickets' => (int)$this->request->getPost('total_tickets'), // Ensure integer value
            'image'         => $imageName,
        ]);
    
        return redirect()->to('/events')->with('success', 'Event published successfully!');
    }
    
    
    public function edit($id)
    {
        $eventModel = new EventModel();
    
        // Fetch the event details by ID
        $data['event'] = $eventModel->find($id);
    
        if (!$data['event']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Event not found');
        }
    
        // Load the edit view
        return view('events/edit', $data);
    }
    
    public function update($id)
    {
        $eventModel = new EventModel();
    
        // Fetch the existing event
        $event = $eventModel->find($id);
    
        // Validate and handle the image upload
        $image = $this->request->getFile('image');
        $eventData = [
            'title'         => $this->request->getPost('title'),
            'sub_title'     => $this->request->getPost('sub_title'),
            'description'   => $this->request->getPost('description'),
            'date'          => $this->request->getPost('date'),
            'time'          => $this->request->getPost('time'),
            'location'      => $this->request->getPost('location'),
            'price'         => $this->request->getPost('price'),
            'total_tickets' => (int)$this->request->getPost('total_tickets'), // Ensure integer value
        ];
    
        // If a new image is uploaded, replace the old one
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // --- Keep original name and make it SEO friendly ---
            $originalName = $image->getName();
            $ext = $image->getExtension();
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            
            $cleanName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nameWithoutExt), '-'));
            
            if (empty($cleanName)) {
                $cleanName = 'event-' . time();
            }
            
            $imageName = $cleanName . '.' . $ext;
            
            $counter = 1;
            while (file_exists($uploadPath . $imageName)) {
                $imageName = $cleanName . '-' . $counter . '.' . $ext;
                $counter++;
            }
            // --------------------------------------------------

            $image->move($uploadPath, $imageName);
            $eventData['image'] = $imageName;
    
            // Optionally, delete the old image file
            if (!empty($event['image']) && file_exists(FCPATH . 'uploads/' . $event['image'])) {
                unlink(FCPATH . 'uploads/' . $event['image']);
            }
        }
    
        // Update the event in the database
        $eventModel->update($id, $eventData);
    
        // Redirect back with a success message
        return redirect()->to('/events')->with('success', 'Event updated successfully!');
    }
    

    public function delete($id)
    {
        // Delete the event and redirect
        $eventModel = new EventModel();
        
        // Optional: Also delete the image file when the event is deleted
        $event = $eventModel->find($id);
        if ($event && !empty($event['image']) && file_exists(FCPATH . 'uploads/' . $event['image'])) {
            unlink(FCPATH . 'uploads/' . $event['image']);
        }

        $eventModel->delete($id);
        
        return redirect()->to('/events')->with('success', 'Event deleted successfully!');
    }
}