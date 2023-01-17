<div class="row pt-2">
    <div class="col-md-12">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{{ __('Attributes') }}</th>
                    <th>{{ __('Value') }}</th>
                </tr>
            </thead>
            <tbody id="attributesTableBody">
                @if( isset($model_attached_attributes) )
                    @foreach( $model_attached_attributes as $model_attached_attribute)
                    <tr>
                        <td>
                            <input name="attribute_id[]" value="{{ $model_attached_attribute->attribute_id }}" type="hidden">
                            <input name="attribute_name[]" readonly value="{{ translation( $model_attached_attribute->attributeDetail->id,13,app()->getLocale(),'attribute_name',$model_attached_attribute->attributeDetail->attribute_name) }}"type="text">
                        </td>
                        <td>
                            <select id="attribueValues{{ $model_attached_attribute->attribute_id }}" class="form-control select2" multiple="multiple" data-tags="true" name="attribute_value{{ $model_attached_attribute->attribute_id }}[]" style="width: 100%">
                                @foreach($model_attached_attribute->allAttributeValue as $a_v)
                                    <option value="{{ $a_v->id }}"
                                        @foreach($model_attached_attribute->attributeValue as $a)
                                            @if($a_v->id == $a->value_id)
                                            selected="selected"
                                            @endif
                                        @endforeach
                                        >{{ $a_v->attribute_value }}</option>
                                @endforeach
                                </select>
                        </td>
                        <td><i class="fa fa-trash"></i></td>
                    </tr>
                    @endforeach
                @endif
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-4">
                                <input name="attribute_id[]" type="hidden">
                                <input class="form-control " autocomplete="off" data-toggle="dropdown" aria-expanded="false" name="attribute_name[]" type="text">
                                <ul id="search-suggestion" class="dropdown-menu" role="menu">
                                </ul>
                            </div>
                        </div>
                    </td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <div class="row">
                    <div class="anchor-links">
                    </div>
                </div>
            </tfoot>
        </table>
    </div>
</div>
