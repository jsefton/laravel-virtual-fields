<?php
namespace JSefton\VirtualFields;

trait VirtualFields
{
    public static function bootVirtualFields()
    {
        static::saving(function ($item) {
            $item->mapAttributes();
        });
    }

    /**
     * Handle data fields to move into data column and clean up before saving
     */
    protected function mapAttributes()
    {
        $data = json_decode($this->data, true);
        $fields = $this->getTableColumns();
        foreach ($this->attributes as $key => $value) {
            if (!in_array($key, $fields)) {
                $data[$key] = $value;
                unset($this->attributes[$key]);
            }
        }
        $this->data = json_encode($data);
    }

    /**
     * Get list of actual fields in the database table for this model
     * @return mixed
     */
    protected function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (!$value) {
            if (isset($this->attributes['data'])) {
                $data = json_decode($this->attributes['data'], true);
                if (isset($data[$key])) {
                    return $data[$key];
                }
            }
        }

        return $value;
    }
}
